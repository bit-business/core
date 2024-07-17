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
                    <button @click="declineAvatar(user.id)" class="btn-decline">Odbij</button>
                    <button @click="approveAvatar(user.id)" class="btn-approve">Odobri</button>
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



    <vs-row class="mt-4">
  <vs-col :vs-lg="6" vs-xs="12">
    <div class="chart-container">
      <h4>Statistika novih korisnika</h4>
      <canvas ref="newUsersChart"></canvas>
    </div>
  </vs-col>
  <vs-col :vs-lg="6" vs-xs="12">
    <div class="chart-container">
      <h4>Narudžbe po mjesecima</h4>
      <canvas ref="newOrdersChart"></canvas>
    </div>
  </vs-col>
</vs-row>


<!-- poruka 
<br><button class="btn-approve" @click="sendFirebaseMessage">Send Firebase Message</button>
-->

</div>
</template>

<script>
import { mapState } from 'vuex'

import skijasiOrder from '../../../../../commerce-module/src/resources/js/api/modules/skijasi-order.js';
import skijasiCart from '../../../../../commerce-theme/src/resources/app/api/modules/skijasi-cart.js';

import Chart from 'chart.js/auto';

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

    newUsersChartData: [],
    newOrdersChartData: null,
    

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

     this.fetchNewUsersPerMonth();
      this.fetchNewOrdersPerMonth();
  },

  beforeDestroy() {
    // Clear the interval when the component is destroyed.
    clearInterval(this.intervalID);
  },



  methods: {
    fetchNewUsersPerMonth() {
    this.$api.skijasiUser.getuserspermonth()
      .then(response => {
     

        // Check for the expected structure
        if (response.data && response.data.items) {
          // Deep clone the items to remove Vue observers
          const clonedItems = JSON.parse(JSON.stringify(response.data.items));
          
          // Transform the data into the desired structure
          this.newUsersChartData = Object.entries(clonedItems).map(([month, users]) => {
            const monthData = { month, Član: 0, 'Običan korisnik': 0 };
            users.forEach(user => {
              monthData[user.user_type] = user.count;
            });
            return monthData;
          });

          this.$nextTick(() => {
            this.createNewUsersChart();
          });
        } else {
          console.error('Unexpected data structure or items is not an array:', response.data);
        }
      })
      .catch(error => {
        console.error('Error fetching new users per month:', error);
      });
  },

  createNewUsersChart() {
  if (!Array.isArray(this.newUsersChartData) || this.newUsersChartData.length === 0) {
    console.error('Invalid chart data:', this.newUsersChartData);
    return;
  }
  const ctx = this.$refs.newUsersChart;
  if (!ctx) {
    console.error('Canvas element not found');
    return;
  }

  const croatianMonths = [
    'Siječanj', 'Veljača', 'Ožujak', 'Travanj', 'Svibanj', 'Lipanj',
    'Srpanj', 'Kolovoz', 'Rujan', 'Listopad', 'Studeni', 'Prosinac'
  ];

  try {
    const labels = this.newUsersChartData.map(item => {
      const [year, monthNumber] = item.month.split('-');
      const monthIndex = parseInt(monthNumber) - 1;
      const shortYear = year.slice(-2); // Get the last two digits of the year
      return `${croatianMonths[monthIndex]} '${shortYear}`;
  
    });
    const dataClan = this.newUsersChartData.map(item => item['Hzuts član']);
    const dataObicanKorisnik = this.newUsersChartData.map(item => item['Običan Korisnik']);

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Hzuts Član',
            data: dataClan,
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
          },
          {
            label: 'Običan Korisnik',
            data: dataObicanKorisnik,
            backgroundColor: 'rgba(255, 149, 132, 0.6)',
          }
        ]
      },
      options: {
        responsive: true,
        scales: {
          x: {
            ticks: {
              maxRotation: 0,
              minRotation: 0
            }
          },
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Broj Novih Korisnika'
            }
          }
        },
        plugins: {
          tooltip: {
            callbacks: {
              label: function(context) {
                let label = context.dataset.label || '';
                if (label) {
                  label += ': ';
                }
                if (context.parsed.y !== null) {
                  label += context.parsed.y.toLocaleString('hr-HR');
                }
                return label;
              }
            }
          }
        }
      }
    });
  } catch (error) {
    console.error('Error creating chart:', error);
  }
},


fetchNewOrdersPerMonth() {
        skijasiOrder.getOrdersPerMonth()
            .then(response => {
              console.log("test:", response);
              if (response && response.items) {
                    this.newOrdersChartData = response.items;
                    console.log("test:", this.newOrdersChartData);
                    this.$nextTick(() => {
                        this.createNewOrdersChart();
                    });
                } else {
                    console.error('Unexpected data structure or items is not an array:', response);
                }
            })
            .catch(error => {
                console.error('Error fetching new orders per month:', error);
            });
    },
    
    createNewOrdersChart() {
    const ctx = this.$refs.newOrdersChart.getContext('2d');

    const croatianMonths = [
        'Siječanj', 'Veljača', 'Ožujak', 'Travanj', 'Svibanj', 'Lipanj',
        'Srpanj', 'Kolovoz', 'Rujan', 'Listopad', 'Studeni', 'Prosinac'
    ];

    if (!this.newOrdersChartData || this.newOrdersChartData.length === 0) {
        console.error('No data available for the chart');
        return;
    }

    try {
        const labels = this.newOrdersChartData.map(item => {
            const [month, year] = item.month.split(' ');
            const monthIndex = parseInt(month, 10) - 1; // Convert month number to zero-based index
            const shortYear = year.slice(-2);
            return `${croatianMonths[monthIndex]} '${shortYear}`;
        });
        const data = this.newOrdersChartData.map(item => item.count);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nove Narudžbe',
                    data: data,
                    backgroundColor: 'rgba(255, 199, 32, 0.6)',
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        ticks: {
                            maxRotation: 0,
                            minRotation: 0
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Broj Novih Narudžbi'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y.toLocaleString('hr-HR');
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error creating chart:', error);
    }
},





  
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
  this.$vs.dialog({
    type: 'confirm',
    color: 'danger',
    title: 'Potvrda brisanja',
    text: 'Jeste li sigurni da želite obrisati zahtjev?',
    acceptText: 'Da',
    cancelText: 'Ne',
    accept: () => {
      // This function will be called if the user confirms
      this.$api.skijasiUser.obrisizahtjev({ id: userId })
        .then(res => {
          // Refresh the requests list after deletion
          this.fetchzahtjeve();
          // Optionally, show a success notification
          this.$vs.notify({
            color: 'success',
            title: 'Uspjeh',
            text: 'Zahtjev je uspješno obrisan.'
          });
        })
        .catch(err => {
          console.error("Error deleting request:", err);
          // Optionally, show an error notification
          this.$vs.notify({
            color: 'danger',
            title: 'Greška',
            text: 'Došlo je do pogreške prilikom brisanja zahtjeva.'
          });
        });
    }
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
  font-size: 0.5rem;
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
  justify-content: center;
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
  gap: 20px;
}

.welcome-container {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  margin-bottom: 1.5rem;

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
  width: 130px; /* Adjust this value to make the icon smaller or larger */
  height: 130px; /* Adjust this value to make the icon smaller or larger */
  margin-left: 30px;
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

.chart-container {
  margin-bottom: 20px;
  padding: 15px;
  background-color: #f8f9fa;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

</style>