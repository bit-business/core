<template>
    <div>
  <vs-row>
    <skijasi-widget :col="col" :widgets="dashboardData"> </skijasi-widget>
    <div style="text-align: center; padding-left: 3%;">Napomena: U analitici su trenutno tesni podaci za testiranje. Ova stranica još nije dovršena.</div>
 
    <div style="text-align: center; padding-left: 3%;">Napomena: U analitici su trenutno tesni podaci za testiranje. Ova stranica još nije dovršena.</div>

  </vs-row>


  <div v-if="unapprovedZahtjevi && unapprovedZahtjevi.length" class="unapproved-container2">
      <h3>Novi zahtjevi za članstvo:          
</h3>
      <div class="scrollable-list">
        <ul>
          <li v-for="user in unapprovedZahtjevi" :key="user.id" class="user-item">
            <button @click="obrisizahtjev(user.id)" class="btn-decline2">Obriši zahtjev</button>
            <img 
            :src="getAvatarUrl(user.new_avatar ? user.new_avatar : user.avatar)" 
              alt="Odbijen Avatar" 
              class="avatar cursor-pointer" 
              @click="showFullscreenImage(user.new_avatar ? user.new_avatar : user.avatar)">
              <div class="user-details">
        <!-- Display user's name and username -->
        <div class="user-info">
            <span>{{ user.name }} {{ user.username }}</span>  
        </div>

        <div class="btncontainer">
            <button @click="otvoriclana(user.id)" class="btn-approve">Pokaži člana</button>
        </div>
    </div>
</li>
        </ul>
      </div>
    </div>


  <div v-if="unapprovedUsers && unapprovedUsers.length" class="unapproved-container">
      <h3>Odobrenja novih profilnih slika:</h3>
      <div class="scrollable-list">
        <ul>
          <li v-for="user in unapprovedUsers" :key="user.id" class="user-item">
            <img 
            :src="getAvatarUrl(user.new_avatar)" 
              alt="Odbijen Avatar" 
              class="avatar cursor-pointer" 
              @click="showFullscreenImage(user.new_avatar)">
              <div class="user-details">
        <!-- Display user's name and username -->
        <div class="user-info">
            <span>{{ user.name }} {{ user.username }}</span> 
        </div>

        <div class="btncontainer">
            <button @click="approveAvatar(user.id)" class="btn-approve">Odobri</button>
            <button @click="declineAvatar(user.id)" class="btn-decline">Odbij</button>
        </div>
    </div>
</li>
        </ul>
      </div>
    </div>

    <!-- Fullscreen image modal -->
    <div v-if="fullscreenImage" class="fullscreen-modal" @click="fullscreenImage = null">
      <img :src="fullscreenImage" class="fullscreen-image"/>
    </div>






<!-- poruka 
<br><button class="btn-approve" @click="sendFirebaseMessage">Send Firebase Message</button>
-->

</div>
</template>

<script>
import { mapState } from 'vuex'
export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: "Početna",
  components: {},
  data: () => ({
    unapprovedUsers: [],
    unapprovedZahtjevi: [],

    fullscreenImage: null, 
    dashboardData: [],
    col: 12,
  }),

  computed: {
    ...mapState({
      isAuthenticated(state) {
        return state.isAuthenticated
      },
      user(state) {
        return state.user
      },
      appName(state) {
        return this.$_.find(state.themeConfigurations, { key: "appName" }).value;
      }
    }),

  },


  mounted() {
    this.getDashboardData();
    this.saveTokenFcmMessage();

    this.fetchUnapprovedAvatars();
    this.fetchzahtjeve();

     // Set up a periodic check every 5 minutes (300000 milliseconds).
     this.intervalID = setInterval(this.fetchUnapprovedAvatars, 300000);
  },

  beforeDestroy() {
    // Clear the interval when the component is destroyed.
    clearInterval(this.intervalID);
  },



  methods: {




    getAvatarUrl(avatarPath) {
    // Base URL from the window location
   // let baseUrl = window.location.origin;
    let baseUrl = `https://hzuts.hr`;


    // Append the specific storage directory and the avatar path
    return `${baseUrl}/storage/${avatarPath}`;
  },

      showFullscreenImage(avatarPath) {  
        this.fullscreenImage = this.getAvatarUrl(avatarPath);
    },

    fetchUnapprovedAvatars() {
        this.$api.skijasiUser.unapprovedAvatars()
            .then(res => {
                this.unapprovedUsers = res;
            })
            .catch(err => {
  
            });
    },

    fetchzahtjeve() {
        this.$api.skijasiUser.novizahtjevclanstvo()
            .then(res => {
                this.unapprovedZahtjevi = res;
            })
            .catch(err => {
  
            });
    },

    otvoriclana(userId) {
      this.$router.push(`/skijasi-dashboard/general/hzuts-clanovi/${userId}`);    
    },
    obrisizahtjev(userId) {
      this.$api.skijasiUser.obrisizahtjev({ id: userId })
            .then(res => {
                // You can refresh the unapproved avatars list after approval
                this.fetchzahtjeve();
            })
            .catch(err => {
                console.error("Error approving avatar:", err);
            }); 
        
    },



    approveAvatar(userId) {
        this.$api.skijasiUser.approveAvatar({ id: userId })
            .then(res => {
                // You can refresh the unapproved avatars list after approval
                this.fetchUnapprovedAvatars();
            })
            .catch(err => {
                console.error("Error approving avatar:", err);
            });
    },
    declineAvatar(userId) {
        this.$api.skijasiUser.declineAvatar({ id: userId })
            .then(res => {
                // You can refresh the unapproved avatars list after declining
                this.fetchUnapprovedAvatars();
            })
            .catch(err => {
                console.error("Error declining avatar:", err);
            });
    },


    getDashboardData() {
      this.$openLoader();
      this.$api.skijasiDashboard
        .index()
        .then((response) => {
          this.$closeLoader();
          this.dashboardData = response.data;
          this.dashboardData.map((data) => {
            data.value =
              data.prefixValue +
              data.value
                .toString()
                .replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, data.delimiter);
            return data;
          });

          if (this.dashboardData.length >= 4) {
            this.col = 3;
          } else if (this.dashboardData.length == 3) {
            this.col = 4;
          } else {
            this.col = 6;
          }
        })
        .catch((error) => {
          if (error.status == 401) {
            this.$closeLoader();
            this.$vs.notify({
              title: this.$t("alert.error"),
              text: error.message,
              color: "danger",
            });
          } else {
            this.$closeLoader();
            this.$vs.notify({
              title: this.$t("alert.danger"),
              text: error.message,
              color: "danger",
            });
          }
        });
    },
    saveTokenFcmMessage() {
      if (this.$statusActiveFeatureFirebase) {
        this.$messagingToken.then((tokenMessage) => {
          try {
            this.$api.skijasiFcm.saveTokenMessage(tokenMessage)
            .then(response => {
            if (response.message) {
                alert(response.message);
            }
        });
          } catch (error) {
            console.error("Errors set token firebase cloud message :", error);
          }
        });
      }
    },



    sendFirebaseMessage() {
  
            this.$api.skijasiFcm.saveTokenMessage("TEST DA LI RADI 2")
            .then(response => {
            if (response.message) {
                alert(response.message);
            }
        });
         


        this.$api.skijasiFcm.sendFirebaseMessage()
        .then(response => {
            if (response.message) {
                alert(response.message);
            }
        })
        .catch(error => {
            console.error("Error sending firebase message:", error);
        });
    }

  },
};
</script>



<style scoped>
.unapproved-container {
  border: 1px solid #e0e0e0;
  background-color: #fff;
  padding: 16px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  max-width: 360px;
  margin-left: 2.5%;
  margin-top: 2%;
}
.unapproved-container2 {
  border: 1px solid #03a9f4;
  background-color: #fff;
  padding: 16px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  max-width: 360px;
  margin-left: 2.5%;
  margin-top: 2%;
}

.scrollable-list {
  max-height: 300px; 
  overflow-y: auto; 
  border: 1px solid #dcdcdc; 
  padding: 8px;
  border-radius: 8px;
}


.avatar {
  width: 72px;
  height: 72px;
  border-radius: 5%;
  object-fit: cover;
}

.btn-approve {
  background-color: #4CAF50; 
  color: white; 
  border: none; 
  padding: 10px 20px;
  cursor: pointer;
  border-radius: 4px;
  font-weight: 400;
}

.btn-decline {
  background-color: #F44336; 
  color: white; 
  border: none; 
  padding: 10px 25px;
  cursor: pointer;
  border-radius: 4px;
  font-weight: 500;
}

.btn-decline2 {
  background-color: #F44336; 
  color: white; 
  border: none; 
  padding: 4px 6px;
  cursor: pointer;
  border-radius: 4px;
  font-weight: 400;
  font-size: 7px;
}

.btn-approve:hover {
  background-color: #45a049;
}

.btn-decline:hover {
  background-color: #e43525;
}
.btn-decline2:hover {
  background-color: #e43525;
}


 /* Fullscreen modal styles */
 .fullscreen-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
  }

  .fullscreen-image {
    max-width: 90%;
    max-height: 90%;
  }
  .btncontainer {
  display: flex;
  justify-content: center;
  align-items: center;
  padding-left: 6%;
  gap: 26px;
}
.user-item {
    display: flex;
    align-items: center;
    gap: 20px;
}

.user-info {
    padding: 1px;
    font-size: 1em;
    text-align: center; /* Center the text */
}

.user-details {
    display: flex;
    flex-direction: column; /* Stack the user-info and btncontainer vertically */
    align-items: center;
    gap: 10px; /* Adjust this value as needed for spacing between user-info and btncontainer */
}


</style>