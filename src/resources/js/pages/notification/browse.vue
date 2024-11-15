<template>
  <div>
    <skijasi-breadcrumb-row>
      <template slot="action"> </template>
    </skijasi-breadcrumb-row>

    <vs-row class="pad-bot">
      <vs-col vs-lg="12">
        <div class="">
          <vs-button class="compose-button" @click="toggleMessageComposer">Pošalji Novu Poruku</vs-button>
        </div>

        <div v-if="showMessageComposer">
          <vs-card class="slanjekartica">
            <template #header>
              <h4>Slanje nove poruke/obavijesti</h4>
            </template>

            <div>
              <div class="input-container">
                <vs-textarea v-model="message" placeholder="Sadržaj poruke" rows="6" />
                <vs-input v-model="url" placeholder="Link koji otvara poruka (neobavezno)" />
                <skijasi-upload-image-poruke
                  v-model="slika"
                  size="3"
                  :label="$t('Slika uz poruku (neobavezno)')"
                  :placeholder="$t('Odaberi sliku')"
                ></skijasi-upload-image-poruke>

                <div>
                  <vs-button :class="{ 'selected-button': !selectSpecificUsers && !filterByHotel && !filterBySeminar && sendToAll === 'svi', 'inactive-button': selectSpecificUsers || filterByHotel || filterBySeminar || sendToAll !== 'svi' }" @click="resetFilters(); sendToAll = 'svi'">Svim korisnicima</vs-button>
                  <vs-button :class="{ 'selected-button': !selectSpecificUsers && !filterByHotel && !filterBySeminar && sendToAll === 'svi2', 'inactive-button': selectSpecificUsers || filterByHotel || filterBySeminar || sendToAll !== 'svi2' }" @click="resetFilters(); sendToAll = 'svi2'">Hzuts članovima</vs-button>
                  <vs-button :class="{ 'selected-button': selectSpecificUsers, 'inactive-button': !selectSpecificUsers }" @click="enableSpecificUsers">Specifični korisnici</vs-button>
                  <vs-button 
  :class="{ 'selected-button': filterByHotel, 'inactive-button': !filterByHotel }" 
  @click="enableHotelFilter">
  Po hotelu
</vs-button>
                  <vs-button :class="{ 'selected-button': filterBySeminar, 'inactive-button': !filterBySeminar }" @click="enableSeminarFilter">Po seminaru</vs-button>
                </div>

        
<!-- Hotel selection -->
<div v-if="filterByHotel" class="mt-4">
  <skijasi-dropdown
    :vs-trigger-click="true"
  >
    <div class="dropdown-toggle">
      {{ selectedHotel || 'Odaberi hotel' }}
    </div>

    <vs-dropdown-menu>
      <div v-if="uniqueHotels.length === 0" class="p-2 text-gray-500">
        No hotels available
      </div>
      <skijasi-dropdown-item 
        v-for="hotel in uniqueHotels"
        :key="hotel"
        @click.native="handleHotelSelect(hotel)"
      >
        {{ hotel }}
      </skijasi-dropdown-item>
    </vs-dropdown-menu>
  </skijasi-dropdown>

  <!-- Show filtered users for hotel -->
  <div class="selected-users" v-if="selectedHotel && filteredUsersByHotel.length">
    <span>Korisnici iz hotela {{ selectedHotel }} ({{ filteredUsersByHotel.length }}):</span>
    <div class="user-list">
      <span 
        v-for="user in filteredUsersByHotel" 
        :key="user.id" 
        class="selected-user"
      >
        {{ user.name }} {{ user.username }}
      </span>
    </div>
  </div>
</div>

<!-- Seminar selection -->
<div v-if="filterBySeminar" class="mt-4">
  <skijasi-dropdown
    :vs-trigger-click="true"
  >
    <div class="dropdown-toggle">
      {{ selectedSeminar || 'Odaberi seminar' }}
    </div>

    <vs-dropdown-menu>
      <div v-if="uniqueSeminars.length === 0" class="p-2 text-gray-500">
        No seminars available
      </div>
      <skijasi-dropdown-item 
        v-for="seminar in uniqueSeminars"
        :key="seminar"
        @click.native="handleSeminarSelect(seminar)"
      >
        {{ seminar }}
      </skijasi-dropdown-item>
    </vs-dropdown-menu>
  </skijasi-dropdown>

  <!-- Show filtered users for seminar -->
  <div class="selected-users" v-if="selectedSeminar && filteredUsersBySeminar.length">
    <span>Korisnici na seminaru {{ selectedSeminar }} ({{ filteredUsersBySeminar.length }}):</span>
    <div class="user-list">
      <span 
        v-for="user in filteredUsersBySeminar" 
        :key="user.id" 
        class="selected-user"
      >
        {{ user.name }} {{ user.username }}
      </span>
    </div>
  </div>
</div>
                <div v-if="selectSpecificUsers">
                  <vs-input v-model="searchQuery" placeholder="Pretraživanje korisnika za slanje poruke" />

                  <div class="user-table">
                    <table class="">
                      <thead>
                        <tr>
                          <th>Ime</th>
                          <th>Prezime</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-if="searchQuery && filteredUsers.length" v-for="user in filteredUsers" :key="user.id" class="user-row">
                          <td>{{ user.name }}</td>
                          <td>{{ user.username }}</td>
                          <td>
                            <vs-button
                              color="primary"
                              icon="add"
                              size="small"
                              flat
                              @click="toggleUserSelection(user)"
                            ></vs-button>
                          </td>
                        </tr>
                        <tr v-if="!searchQuery && users.length" class="user-row">
                          <td colspan="3" class="no-data-message">
                            Molimo upišite ime za pretraživanje HZUTS korisnika
                          </td>
                        </tr>
                        <tr v-if="!users.length" class="user-row">
                          <td colspan="3" class="no-data-message">
                            Nema korisnika po tome imenu.
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <div class="selected-users">
                    <span v-if="selectedUsers.length">Korisnici kojima će se poslati poruka:</span>
                    <span v-for="(user, index) in selectedUsers" :key="index" class="selected-user">
                      {{ user.username }} {{ user.name }}<span v-if="index !== selectedUsers.length - 1">,</span>
                    </span>
                  </div>
                </div>
                <div v-else>
                  <p v-if="sendToAll === 'svi'">Poruka će biti poslana svim korisnicima.</p>
                  <p v-else-if="sendToAll === 'svi2'">Poruka će biti poslana HZUTS članovima.</p>
                </div>
              </div>
            </div>

            <div class="button-group">
              <div class="con-btn p-5">
                <vs-button class="posaljigumb" color="warning" @click="sendMessage">Pošalji Poruku</vs-button>
                <vs-button class="posaljigumb" color="gray" @click="toggleMessageComposer">Odustani</vs-button>
              </div>
            </div>
          </vs-card>
        </div>
      </vs-col>
    </vs-row>
    
    <vs-row>
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header" class="header-container">
            <h3>Povijest poslanih poruka</h3>
          </div>

          <div>
            <skijasi-table
              v-model="selected"
              pagination
              max-items="10"
              search
              :data="sortedMessages"
              stripe
              description
              :description-items="descriptionItems"
              :description-title="$t('role.footer.descriptionTitle')"
              :description-connector="$t('role.footer.descriptionConnector')"
              :description-body="$t('role.footer.descriptionBody')"
            >
              <template slot="thead">
                <vs-th>Poruku kreirao</vs-th>
                <vs-th>Slika</vs-th>
                <vs-th>Sadržaj poruke</vs-th>
                <vs-th>Link</vs-th>
                <vs-th>Poslano korisnicima</vs-th>
                <vs-th>Vrijeme slanja</vs-th>
                <vs-th>Tko je pročitao poruku</vs-th>
                <vs-th>Opcije</vs-th>
              </template>

              <template slot-scope="{ data }">
                <vs-tr v-for="(message, index) in data" :key="index" :data="message" style="text-align: center;">
                  <vs-td v-if="message">
                    {{ message.sent_by_user ? `${message.sent_by_user.username} ${message.sent_by_user.name}` : 'Unknown User' }}
                  </vs-td>
                  <vs-td>
                    <img :src="message.slika" alt="Nema slike" style="max-width: 100px; max-height: 100px;">
                  </vs-td>
                  <vs-td style="max-width: 30vw; word-wrap: break-word;">
                    {{ message.message }}
                  </vs-td>
                  <vs-td>{{ message.url }}</vs-td>
                  <vs-td>
  <template v-if="message && message.sent_to_users">
    <div class="recipient-list">
      <!-- Handle string values (svi/svi2) -->
      <span v-if="typeof message.sent_to_users === 'string'">
        {{ message.sent_to_users === 'svi' ? 'Poslano svima' : 
           message.sent_to_users === 'svi2' ? 'Poslano članovima' : 
           message.sent_to_users }}
      </span>
      <!-- Handle array of users -->
      <template v-else-if="Array.isArray(message.sent_to_users)">
        <div>
          <div class="recipient-count">Poslano korisnicima ({{ message.sent_to_users.length }})</div>
          <div class="recipients-scroll">
            <span v-for="(recipient, index) in message.sent_to_users" :key="index" class="recipient">
              {{ recipient.username }}{{ recipient.name ? ` ${recipient.name}` : '' }}
              <span v-if="index !== message.sent_to_users.length - 1">, </span>
            </span>
          </div>
        </div>
      </template>
      <span v-else>Greška. Javit administratoru.</span>
    </div>
  </template>
  <span v-else>Nema primatelja</span>
</vs-td>
                  <vs-td v-if="message && message.created_at">{{ formatDate(message.created_at) }}</vs-td>
                  <vs-td>
  <template v-if="message && message.readers">
    <div class="reader-list">
      <span v-if="typeof message.readers === 'string' && ['svi', 'svi2'].includes(message.readers)">
        Svi pročitali
      </span>
      <span v-else-if="typeof message.readers === 'string'">
        {{ message.readers }}
      </span>
      <template v-else-if="Array.isArray(message.readers)">
        <span v-for="(reader, index) in message.readers" :key="index" class="recipient">
          {{ reader.username }} {{ reader.name }}
        </span>
      </template>
    </div>
  </template>
  <span v-else>Nitko nije pročitao</span>
</vs-td>
                  <vs-td>
                    <skijasi-dropdown-item icon="delete" @click="deleteMessage(message.id)">
                      Obriši
                    </skijasi-dropdown-item>
                    <skijasi-dropdown-item icon="check" @click="markAsRead(message)">
                      Označi kao pročitano svima
                    </skijasi-dropdown-item>
                  </vs-td>
                </vs-tr>
              </template>
            </skijasi-table>
          </div>
        </vs-card>
      </vs-col>
    </vs-row>
  </div>
</template>

<script>
import { mapState } from 'vuex'

export default {
  name: "NotificationBrowse",
  data() {
    return {
      showMessageComposer: false,
      message: "",
      slika: null,
      url: "",
      sendToAll: '',
      sentAt: null,
      selectSpecificUsers: false,
      selectedUsers: [],
      users: [],
      descriptionItems: [10, 50, 100],
      selected: [],
      messages: [],
      searchQuery: '',
      tableHeight: '20vh',

      records: {
      '*items': [],
      uniqueHotels: [],
      uniqueSeminars: []
    },
      filterByHotel: false,
      filterBySeminar: false,
      selectedHotel: null,
      selectedSeminar: null,
    };
  },

  created() {
  try {
    this.getUserList();
    this.fetchUserMessages();
    this.loadFormEntries().then(() => {
      this.logDataStructure();
    }).catch(error => {
      console.error('Error in loadFormEntries:', error);
    });
  } catch (error) {
    console.error('Error in created hook:', error);
  }
},

  computed: {
    sortedMessages() {
      return this.messages.slice().sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    },

    filteredUsers() {
      const query = this.searchQuery.trim().toLowerCase();
      return this.users.filter(user => {
        const username = user.username.trim().toLowerCase();
        const name = user.name.trim().toLowerCase();
        return username.includes(query) || name.includes(query) || `${name} ${username}`.includes(query);
      });
    },



    uniqueHotels() {
    const hotels = this.records?.uniqueHotels || [];
    console.log('Available hotels:', hotels);
    return hotels;
  },

  uniqueSeminars() {
    const seminars = this.records?.uniqueSeminars || [];
    console.log('Available seminars:', seminars);
    return seminars;
  },

  filteredUsersByHotel() {
    if (!this.selectedHotel || !this.records?.['*items']) {
      return [];
    }
    
    return this.records['*items']
      .filter(record => record.hotel === this.selectedHotel)
      .map(record => {
        let userData = record.data;
        try {
          if (typeof userData === 'string') {
            userData = JSON.parse(userData);
          }
          
          if (!record.userid) {
            console.warn('No userid found for record:', record);
            return null;
          }

          return {
            id: record.id, // Original id
            userid: record.userid, // userid for sending messages
            name: `${userData.name || ''} ${userData.surname || ''}`.trim(),
            username: userData.username || userData.email || '',
            data: userData
          };
        } catch (e) {
          console.error('Error parsing user data:', e);
          return null;
        }
      })
      .filter(user => user && user.userid); // Filter by userid instead of id
  },

  filteredUsersBySeminar() {
    if (!this.selectedSeminar || !this.records?.['*items']) {
      return [];
    }
    
    return this.records['*items']
      .filter(record => record.formName === this.selectedSeminar)
      .map(record => {
        let userData = record.data;
        try {
          if (typeof userData === 'string') {
            userData = JSON.parse(userData);
          }
          
          if (!record.userid) {
            console.warn('No userid found for record:', record);
            return null;
          }

          return {
            id: record.id, // Original id
            userid: record.userid, // userid for sending messages
            name: `${userData.name || ''} ${userData.surname || ''}`.trim(),
            username: userData.username || userData.email || '',
            data: userData
          };
        } catch (e) {
          console.error('Error parsing user data:', e);
          return null;
        }
      })
      .filter(user => user && user.userid); // Filter by userid instead of id
  },

  },
  mounted() {
  this.$nextTick(() => {
    this.debugData();
  });
},
  methods: {
    logDataStructure() {
        console.log('Current state:', {
            entries: this.entries,
            records: this.records,
            uniqueHotels: this.uniqueHotels,
            uniqueSeminars: this.uniqueSeminars,
            selectedHotel: this.selectedHotel,
            selectedSeminar: this.selectedSeminar,
            filteredUsersByHotel: this.filteredUsersByHotel,
            filteredUsersBySeminar: this.filteredUsersBySeminar
        });
    },

    debugData() {
    console.log('Records structure:', {
      records: this.records,
      hotels: this.uniqueHotels,
      seminars: this.uniqueSeminars,
      selectedHotel: this.selectedHotel,
      selectedSeminar: this.selectedSeminar,
      filteredHotelUsers: this.filteredUsersByHotel,
      filteredSeminarUsers: this.filteredUsersBySeminar
    });
  },


    enableHotelFilter() {
      this.resetFilters();
      this.filterByHotel = true;  // Set the data property
      this.sendToAll = '';
    },

    enableSeminarFilter() {
      this.resetFilters();
      this.filterBySeminar = true;  // Set the data property
      this.sendToAll = '';
    },


    async loadFormEntries() {
  try {
    this.$openLoader();
    const response = await this.$api.skijasi.getFormEntries();
    console.log('Raw API Response:', response);
    
    if (response?.data) {
      // Extract data from Laravel collection objects
      let entries = [];
      let hotels = [];
      let seminars = [];

      // Handle entries collection
      if (response.data.entries && response.data.entries['\u0000*\u0000items']) {
        entries = response.data.entries['\u0000*\u0000items'];
      }

      // Handle uniqueHotels collection
      if (response.data.uniqueHotels && response.data.uniqueHotels['\u0000*\u0000items']) {
        hotels = response.data.uniqueHotels['\u0000*\u0000items'];
      }

      // Handle uniqueSeminars collection
      if (response.data.uniqueSeminars && response.data.uniqueSeminars['\u0000*\u0000items']) {
        seminars = response.data.uniqueSeminars['\u0000*\u0000items'];
      }

      // Store the extracted data
      this.records = {
        '*items': entries,
        uniqueHotels: hotels,
        uniqueSeminars: seminars
      };

      console.log('Processed records:', {
        entriesCount: entries.length,
        hotels: hotels,
        seminars: seminars
      });
    }
  } catch (error) {
    console.error('Error loading form entries:', error);
    this.$vs.notify({
      title: 'Error',
      text: 'Failed to load entries',
      color: 'danger'
    });
  } finally {
    this.$closeLoader();
  }
},

    handleHotelSelect(hotel) {
      console.log('Selecting hotel:', hotel);
      this.selectedHotel = hotel;
      this.selectedUsers = [...this.filteredUsersByHotel];
      console.log('Selected users:', this.selectedUsers);
    },

    handleSeminarSelect(seminar) {
      console.log('Selecting seminar:', seminar);
      this.selectedSeminar = seminar;
      this.selectedUsers = [...this.filteredUsersBySeminar];
      console.log('Selected users:', this.selectedUsers);
    },

    resetFilters() {
      this.selectSpecificUsers = false;
      this.filterByHotel = false;
      this.filterBySeminar = false;
      this.selectedHotel = null;
      this.selectedSeminar = null;
      this.selectedUsers = [];
    },



// Make sure the groupEntriesByForm method matches the backend data structure
groupEntriesByForm(records) {
  if (!Array.isArray(records)) return [];

  return Object.values(records.reduce((acc, record) => {
    const formId = record.formId;
    
    if (!acc[formId]) {
      acc[formId] = {
        formId: formId,
        formName: record.formName,
        entries: []
      };
    }
    
    acc[formId].entries.push(record);
    return acc;
  }, {}));
},





    resetFilters() {
      this.selectSpecificUsers = false;
      this.filterByHotel = false;
      this.filterBySeminar = false;
      this.selectedHotel = null;
      this.selectedSeminar = null;
      this.selectedUsers = [];
    },

    enableSpecificUsers() {
      this.resetFilters();
      this.selectSpecificUsers = true;
      this.sendToAll = '';
    },




    toggleUserSelection(user) {
      const index = this.selectedUsers.findIndex(u => u.id === user.id);
      if (index === -1) {
        this.selectedUsers.push(user);
      } else {
        this.selectedUsers.splice(index, 1);
      }
    },

    getUserList() {
      this.$openLoader();
      this.$api.skijasiUser
        .browseuserporuke()
        .then((response) => {
          this.$closeLoader();
          this.users = response.data.users;
        })
        .catch((error) => {
          this.$closeLoader();
          this.$vs.notify({
            title: this.$t("alert.danger"),
            text: error.message,
            color: "danger",
          });
        });
    },

    toggleMessageComposer() {
      this.showMessageComposer = !this.showMessageComposer;
      if (!this.showMessageComposer) {
        this.resetForm();
      }
    },

    resetForm() {
      this.message = "";
      this.slika = null;
      this.sendToAll = '';
      this.selectedUsers = [];
      this.url = "";
    },

    sendMessage() {
  let messageData = {
    url: this.url,
    slika: this.slika,
    message: this.message,
  };

  if (this.filterByHotel && this.selectedHotel) {
    messageData.sendToAll = 'specific';
    messageData.sentTo = this.filteredUsersByHotel
      .map(user => user.userid.toString())
      .filter(Boolean);
  } else if (this.filterBySeminar && this.selectedSeminar) {
    messageData.sendToAll = 'specific';
    messageData.sentTo = this.filteredUsersBySeminar
      .map(user => user.userid.toString())
      .filter(Boolean);
  } else if (this.selectSpecificUsers) {
    messageData.sendToAll = 'specific';
    messageData.sentTo = this.selectedUsers
      .map(user => user.userid.toString())
      .filter(Boolean);
  } else {
    messageData.sendToAll = this.sendToAll;
    messageData.sentTo = [];
  }

  console.log('Message Data:', messageData);

  // Add validation
  if (messageData.sendToAll === 'specific' && (!messageData.sentTo || messageData.sentTo.length === 0)) {
    this.$vs.notify({
      title: "Greška",
      text: "Niste odabrali korisnike za slanje poruke",
      color: "danger",
    });
    return;
  }


  // Make sure sentTo is an array even when empty
  if (!Array.isArray(messageData.sentTo)) {
    messageData.sentTo = [];
  }

  this.$api.skijasiPoruke
    .sendMessage(messageData)
    .then((response) => {
      console.log('Success response:', response);
      this.fetchUserMessages();
      this.$vs.notify({
        title: "Poruka poslana",
        text: "Slanje uspješno!",
        color: "success",
      });
      this.toggleMessageComposer();
    })
    .catch((error) => {
      console.error('Error response:', error.response?.data);
      console.error('Sent data:', messageData);
      this.$vs.notify({
        title: "Greška",
        text: error.response?.data?.message || "Došlo je do pogreške prilikom slanja poruke.",
        color: "danger",
      });
    });
},


    deleteMessage(messageId) {
      this.$api.skijasiPoruke
        .deleteMessage(messageId)
        .then(() => {
          this.messages = this.messages.filter((message) => message.id !== messageId);
        })
        .catch((error) => {
          console.error("Error deleting message:", error);
        });
    },

    markAsRead(message) {
  if (message.sent_to_users) {
    let recipients;

    if (typeof message.sent_to_users === 'string') {
      // Convert display text back to the correct database value
      if (message.sent_to_users === 'Poslano svima') {
        recipients = ['svi'];
      } else if (message.sent_to_users === 'Poslano članovima') {
        recipients = ['svi2'];
      } else {
        recipients = [message.sent_to_users];
      }
    } else if (Array.isArray(message.sent_to_users)) {
      recipients = message.sent_to_users.map(user => {
        if (user && typeof user === 'object' && user.id) {
          return user.id.toString();
        } else if (typeof user === 'string') {
          return user;
        }
        return null;
      }).filter(id => id !== null);
    } else {
      console.error("Error: Unexpected sent_to_users format", message.sent_to_users);
      return;
    }

    if (recipients.length === 0) {
      console.error("Error: No valid recipients found", message.sent_to_users);
      return;
    }

    console.log("Sending recipients:", recipients); // For debugging

    this.$api.skijasiPoruke
      .markallread(message.id, { recipients: recipients })
      .then((response) => {
        const index = this.messages.findIndex((msg) => msg.id === message.id);
        if (index !== -1) {
          this.messages[index].readers = message.sent_to_users;
          if (response.data && response.data.is_read) {
            this.messages[index].is_read = response.data.is_read;
          }
        }
      })
      .catch((error) => {
        console.error("Error marking message as read:", error);
        if (error.response) {
          console.error("Response data:", error.response.data);
          console.error("Response status:", error.response.status);
          console.error("Response headers:", error.response.headers);
        }
      });
  } else {
    console.error("Error: Message has no recipients", message);
  }
},

    fetchUserMessages() {
      this.$api.skijasiPoruke
        .getMessages()
        .then((response) => {
          if (response) {
            this.messages = response;
          } else {
            console.error("No data received from API");
          }
        })
        .catch((error) => {
          console.error("Error fetching user messages:", error);
        });
    },

    formatDate(dateString) {
      return new Date(dateString).toLocaleString();
    },
  },
};
</script>


<style scoped>
.user-list {
  display: flex;
  flex-wrap: wrap;
}

.user-list span {
  padding: 5px;
  cursor: pointer;
}

.user-list span.selected {
  background-color: #20c4ff;
  color: white;
}

.user-list span.select-all {
  margin-top: 10px;
  cursor: pointer;
}


.selected-button {
  background-color: #06bbd3 !important;
  color: white;
  font-weight: bold;
}

.inactive-button {
  background-color: #ccc !important;
  color: #aaaaaa !important;
}

.header-container {
  display: flex;

  align-items: center;
}

.compose-button {
  width: 100%;
  margin: 0 auto;
  padding: 1%;
  font-size: large;
  font-weight: bold;
}

.pad-bot {
padding-bottom: 3%;
padding-top: 1%;
}

.recipient-list {
  display: flex;
  flex-wrap: wrap;
  font-size: smaller;
}
.reader-list{
  display: flex;
  flex-wrap: wrap;
  font-size: smaller;
  color: rgb(15, 79, 85);
}

.recipient {
  margin-right: 10px; /* Adjust as needed */
}

.input-container {
  display: flex;
  flex-direction: column;
  gap: 1rem; /* Adjust the gap value as needed */
}

.slanjekartica{
background-color:rgb(253, 253, 253);
border: #06bbd3 2px solid;
}
.button-group {
  margin-top: 20px;
  display: flex;
  justify-content: center; /* Center horizontally */
}

.con-btn {
  display: flex;
  justify-content: center; /* Center buttons */
  align-items: center; /* Align items vertically */
  background-color: rgb(253, 253, 253);
}

.posaljigumb {
  margin-right: 50px; 
  padding: 15px 30px; 

  font-weight: 600;
  font-size: small ;
}


.user-table {
  max-height: 20vh; /* Adjust the height as needed */
  overflow-y: auto; /* Add scrollbars for vertical overflow */
}

.selected-users {
  margin-top: 1rem;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
}

.selected-user {
  margin-left: 0.5rem;
  background-color: #f0f0f0;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
}

.user-row td {
  padding: 0.5rem 1rem; /* Adjust the padding values as needed */
}

.user-table table {
  border: 1px solid #06bbd3; /* Add a gray border to the table */
  border-collapse: collapse; /* Merge the borders between cells */
}

.user-table th,
.user-table td {
 /* Add a gray border to table cells */
  padding: 0.5rem; /* Add some padding to the cells */
}



.selected-users {
  margin-top: 1rem;
}

.user-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.selected-user {
  background-color: #f0f0f0;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.875rem;
}

.mt-4 {
  margin-top: 1rem;
}

.dropdown-toggle {
  padding: 7px 12px;
  cursor: pointer;
  border: 1px solid #ccc;
  border-radius: 4px;
  min-width: 150px;
}



</style>