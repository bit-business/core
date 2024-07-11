<template>
    <div>
  <vs-row>

    <div class="welcome-container">
        <img src="/storage/slike/g14.svg" class="centered-image" alt="Welcome Image">
    <div>
        <div class="welcome-text">Dobrodošli na Hzuts bazu i nadzornu ploču</div>
        <div class="sub-text">Ovdje se nalazi statistika i svi novi zahtjevi od korisnika </div> 
      </div>
    </div>


    <skijasi-widget :col="col" :widgets="dashboardData"> </skijasi-widget>

  
  </vs-row>

  <vs-row class="mt-4">
      <vs-col :vs-lg="col" vs-xs="12">
        <div class="notification-section">
          <div class="notification-header">
            <h4>Nove narudžbe (čekaju odobrenje)</h4>
            <vs-button
              color="primary"
              type="border"
              size="small"
              @click="otvoriplacanja"
            >
              Prikaži
            </vs-button>
          </div>
          <div class="icon-container">
        <span :class="['icon-text', { 'animate-pulse': newOrdersCount > 0 }]">
          {{ newOrdersCount }}
        </span>
        <img src="/storage/slike/baza/placanjeikona1.svg" alt="Nove narudžbe" class="icon">
      </div>
        </div>
      </vs-col>
      <vs-col :vs-lg="col" vs-xs="12">
        <div class="notification-section">
          <div class="notification-header">
            <h4>Završene narudžbe</h4>
            <vs-button
              color="primary"
              type="border"
              size="small"
              @click="otvoriplacanja"
            >
              Prikaži
            </vs-button>
          </div>
          <div class="icon-container">
            <span class="icon-text">  {{ uspjesneOrdersCount }}</span> 
            <img src="/storage/slike/baza/placanjeikona2.svg" alt="Završene narudžbe" class="icon">
   
          </div>
        </div>
      </vs-col>
      <vs-col :vs-lg="col" vs-xs="12">
        <div class="notification-section">
          <div class="notification-header">
            <h4>Broj aktivnih zaduženja</h4>
            <vs-button
              color="primary"
              type="border"
              size="small"
              @click="otvoricart"
            >
              Prikaži
            </vs-button>
          </div>
          <div class="icon-container">
            <span class="icon-text">{{ totalCartItems }}</span> 
            <img src="/storage/slike/baza/placanjeikona3.svg" alt="Aktivna zaduženja" class="icon">

          </div>
        </div>
      </vs-col>
    </vs-row>

    <vs-row class="mt-4">
      <vs-col :vs-lg="col" vs-xs="12" v-if="unapprovedZahtjevi && unapprovedZahtjevi.length">
        <div class="section-container unapproved-container2">
          <h3>Novi zahtjevi za članstvo:</h3>
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
      </vs-col>

      <vs-col :vs-lg="col" vs-xs="12" v-if="unapprovedUsers && unapprovedUsers.length">
        <div class="section-container unapproved-container">
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
      </vs-col>
    </vs-row>

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

import skijasiOrder from '../../../../../commerce-module/src/resources/js/api/modules/skijasi-order.js';
import skijasiCart from '../../../../../commerce-theme/src/resources/app/api/modules/skijasi-cart.js';


export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: "Početna",
  components: {
  },
  data: () => ({
    unapprovedUsers: [],
    unapprovedZahtjevi: [],

    fullscreenImage: null, 
    dashboardData: [],
    col: 12,

    orders: [], 
    totalCartItems: 0,

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

        newOrdersCount() {
      return this.orders.filter(order => order.status == 'waitingSellerConfirmation').length;
    },
    uspjesneOrdersCount() {
      return this.orders.filter(order => order.status == 'done').length;
    },

      
  

  },


  mounted() {
    this.getDashboardData();
    this.saveTokenFcmMessage();

    this.fetchUnapprovedAvatars();
    this.fetchzahtjeve();

    this.fetchOrders();
 
     // Set up a periodic check every 5 minutes (300000 milliseconds).
     this.intervalID = setInterval(this.fetchUnapprovedAvatars, 300000);
  },

  beforeDestroy() {
    // Clear the interval when the component is destroyed.
    clearInterval(this.intervalID);
  },



  methods: {
    fetchTotalCartItems() {
      skijasiCart.getTotalItemsCart()
        .then(response => {
        
            this.totalCartItems = response.data.totalItems;
            console.log('Updated totalCartItems:', this.totalCartItems);
          
        })
        .catch(error => {
          console.error('Error fetching total cart items:', error);
        });
    },

    fetchOrders() {
  skijasiOrder.browse()
    .then(res => {
      if (res.data && res.data.orders && res.data.orders.data) {
        this.orders = res.data.orders.data;
      } else {
        this.orders = [];
      }
    })
    .catch(err => {
      this.$helper.displayErrors(err);
    })
    .finally(() => {
      // Any final actions
      this.fetchTotalCartItems();
    });
},

//     clearAllNotifications() {
//   this.$vs.dialog({
//     type: 'confirm',
//     color: 'danger',
//     title: 'Potvrda',
//     text: 'Jeste li sigurni da želite obrisati sve obavijesti?',
//     acceptText: 'Da',
//     cancelText: 'Ne',
//     accept: () => {
//       this.$api.skijasiFcm.clearAllNotifications()
//         .then((response) => {
//           if (response.status === 200) {

//             if (this.$refs.notificationComponent) {
//                 this.$refs.notificationComponent.getMessages();
//                 this.$refs.notificationComponent.updateNotificationCount();
//               }
//             this.$vs.notify({
//               color: 'success',
//               title: 'Uspjeh',
//               text: response.data.message || 'Sve obavijesti su obrisane.'
//             });
//           }
//         })
//         .catch((error) => {
//           console.error("Error clearing notifications:", error);
//           this.$vs.notify({
//             color: 'danger',
//             title: 'Greška',
//             text: 'Došlo je do pogreške prilikom brisanja obavijesti.'
//           });
//         });
//     }
//   });
// },
  
    otvoriplacanja() {
      this.$router.push(`/skijasi-dashboard/order/`);    
    },
    otvoricart() {
      this.$router.push(`/skijasi-dashboard/cart/`);    
    },


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


/*
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
*/


  },
};
</script>


<style scoped>
.mt-4 {
  margin-top: 2rem;
}

.section-container {
  border: 1px solid #e0e0e0;
  background-color: #fff;
  padding: 16px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  height: 100%;
}


.unapproved-container2 {
  /* border-color: #03a9f4; */
}

.scrollable-list {
  max-height: 300px; 
  overflow-y: auto; 
  border: 1px solid #dcdcdc; 
  padding: 8px;
  border-radius: 8px;
}

.avatar {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  object-fit: cover;
}

.btn-approve, .btn-decline, .btn-decline2 {
  border: none; 
  padding: 8px 16px;
  cursor: pointer;
  border-radius: 4px;
  font-weight: 500;
  font-size: 0.9rem;
}

.btn-approve {
  background-color: #4CAF50; 
  color: white; 
}

.btn-decline {
  background-color: #F44336; 
  color: white; 
}

.btn-decline2 {
  background-color: #F44336; 
  color: white; 
  font-size: 0.8rem;
  padding: 4px 8px;
}

.btn-approve:hover, .btn-decline:hover, .btn-decline2:hover {
  opacity: 0.9;
}

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

.user-item {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}

.user-info {
  font-size: 0.9em;
}

.user-details {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.btncontainer {
  display: flex;
  gap: 10px;
}

.welcome-container {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  margin-bottom: 2rem;
  margin-top: 1rem;
}

.centered-image {
  width: 4%;
  margin-right: 1rem;
}

.welcome-text {
  font-size: 20px;
  font-weight: 700;
}

.sub-text {
  font-size: 14px;
  color: #888;
}


.notification-section {
  background-color: #fff;
  padding: 16px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: 0.5s ease-out;
overflow: visible;
border: 1px solid #e0e0e0;
}

.notification-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.icon-container {
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon {
  width: 140px; /* Adjust this value to make the icon smaller or larger */
  height: 140px; /* Adjust this value to make the icon smaller or larger */
  margin-left: 34px;
}

.icon-text {
  font-size: 29px;
  font-weight: bold;
  color: #333;
}

@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.17);
  }
  100% {
    transform: scale(1);
  }
}

.animate-pulse {
  animation: pulse 2s infinite;
color: orange;
}



.notification-section:hover {
 box-shadow: 0 4px 18px 0 rgba(0, 0, 0, 0.25);
}


</style>