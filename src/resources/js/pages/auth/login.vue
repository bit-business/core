<!--
<template>
  <vs-col vs-lg="12" class="main-container__box--auth">
    <vs-card class="main-container__card--auth">
      <skijasi-auth-card-header slot="header">{{
        $t("login.title")
      }}</skijasi-auth-card-header>
      <div>
        <form @submit="login()">
          <vs-input
            icon="person"
            icon-after
            size="default"
            :placeholder="$t('login.field.email')"
            v-model="email"
            class="login__input"
          />
          <div v-if="errors.email" class="login__error-container">
            <div v-if="$helper.isArray(errors.email)">
              <span
                class="login__input--error"
                v-for="(info, index) in errors.email"
                :key="index"
              >
                {{ info }}
              </span>
            </div>
            <div v-else>
              <span class="login__input--error" v-html="errors.email"></span>
            </div>
          </div>
          <vs-input
            icon="lock"
            type="password"
            icon-after
            size="default"
            :placeholder="$t('login.field.password')"
            v-model="password"
            class="login__input"
            @keyup.enter="login()"
          />
          <div v-if="errors.password" class="login__error-container">
            <div v-if="$helper.isArray(errors.password)">
              <span
                class="login__input--error"
                v-for="(info, index) in errors.password"
                :key="index"
              >
                {{ info }}
              </span>
            </div>
            <div v-else>
              <span class="login__input--error" v-html="errors.password"></span>
            </div>
          </div>

          <div class="login__footer">
            <div
              class="vs-component con-vs-checkbox vs-checkbox-primary vs-checkbox-default"
            >
              <input
                type="checkbox"
                class="vs-checkbox--input"
                v-model="rememberMe"
              /><span
                class="checkbox_x vs-checkbox"
                style="border: 2px solid rgb(180, 180, 180)"
                ><span class="vs-checkbox--check"
                  ><i
                    class="vs-icon notranslate icon-scale vs-checkbox--icon material-icons null"
                    >check</i
                  ></span
                ></span
              ><span class="con-slot-label">{{ $t("login.rememberMe") }}</span>
            </div>
            <router-link
              :to="'/' + baseUrl + '/forgot-password'"
              class="login__forgot-password"
              >{{ $t("login.forgotPassword") }}</router-link
            >
          </div>
          <vs-button type="relief" class="login__button" @click="login()">{{
            $t("login.button")
          }}</vs-button>
        </form>
        <div class="login__register-link">
          {{ $t("login.createAccount.text") }} &nbsp;
          <router-link :to="'/' + baseUrl + '/register'">{{
            $t("login.createAccount.link")
          }}</router-link>
        </div>
      </div>
    </vs-card>
  </vs-col>
</template>
-->

<template>
  <div class="dashboardlogin">
    <div class="sign-in-button">
          <div class="path-container">
            <img class="path-container-child" alt="" src="/storage/slike/dashboardlogin/vector-21.svg" />
            <img
              class="clip-path-group"
              loading="lazy"
              alt=""
              src="/storage/slike/dashboardlogin/clip-path-group.svg"
            />
          </div>
        </div>
        <div class="sign-in-button1">
          <div class="vector-parent">
            <img class="vector-icon" alt="" src="/storage/slike/dashboardlogin/vector-22.svg" />
            <img class="g14-icon" loading="lazy" alt="" src="/storage/slike/dashboardlogin/g14.svg" />
          </div>
        </div>
        
    <h1 class="dobro-doli">Dobro došli</h1>
    <section class="dashboardlogin-inner">
      <div class="frame-parent18">
        <div class="prijavi-se-wrapper">
          <h1 class="prijavi-se">Prijavi se</h1>
        </div>

        <form @submit="login()">
          
        <div class="email-parent">
          <div class="lozinka-parent">
          <div class="email">Email</div>
          <div class="email-label-parent">
          <img
            class="email-label-icon"
            loading="lazy"
            alt=""
            src="/storage/slike/dashboardlogin/email-label.svg"
          />
          <div class="email-container">
        <input
          type="email"
          class="email-input"
          placeholder="Unesi email"
          ref="emailInput"
          v-model="email"
        />    </div>
      </div>
    </div>
        </div>
        <div class="frame-parent19">
          <div class="lozinka-parent">
    <div class="lozinka">Lozinka</div>
    <div class="email-label-parent">
      <img
        class="email-label-icon1"
        alt=""
        src="/storage/slike/dashboardlogin/password_label.svg"
      />
      <div class="password-container">
        <input
          type="password"
          class="password-input"
          placeholder="Unesi lozinku"
          ref="passwordInput"
          v-model="password"
        />
        <img
          class="eye-icon"
          alt=""
          src="/storage/slike/dashboardlogin/eyeoff-1.svg"
          ref="eyeIcon"
          @click="togglePasswordVisibility"
        />
      </div>
 

  </div>
</div>
          <div class="frame-parent20">
            <div class="rectangle-parent55">
              <input class="rectangle-input" type="checkbox"  v-model="rememberMe" />
              <div class="zapamti-me">Zapamti me</div>
            </div>
          
            <router-link
              :to="'/' + baseUrl + '/forgot-password'"
              class="zaboravljena-lozinka"
              >{{ $t("login.forgotPassword") }}</router-link
            >


          </div>
        </div>        </form>
        <button class="login-with-email-button" @click="login()">
          <div class="login-with-email-button-child" />
          <div class="prijavi-se1">Prijavi se</div>
        </button>


        <div class="nema-raun-kontaktiraj-admin-wrapper">
          <div class="nema-raun-kontaktiraj-container">
            <span>Nemaš račun? </span>
            <span class="kontaktiraj-administratora"
              >Kontaktiraj administratora</span
            >
          </div>
        </div>
  
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: "AuthLogin",
  data: () => ({
    email: "",
    password: "",
    baseUrl: process.env.MIX_ADMIN_PANEL_ROUTE_PREFIX
      ? process.env.MIX_ADMIN_PANEL_ROUTE_PREFIX
      : "skijasi-dashboard",
    rememberMe: false,
    errors: {},
  }),

  mounted() {
    this.initializePasswordToggle();
  },
  methods: {
    initializePasswordToggle() {
      this.passwordInput = this.$refs.passwordInput;
      this.eyeIcon = this.$refs.eyeIcon;
    },
    togglePasswordVisibility() {
      if (this.passwordInput.type === 'password') {
        this.passwordInput.type = 'text';
        this.eyeIcon.src = '/storage/slike/dashboardlogin/eye-1.svg';
      } else {
        this.passwordInput.type = 'password';
        this.eyeIcon.src = '/storage/slike/dashboardlogin/eyeoff-1.svg';
      }
    },


  
    login() {
      this.$openLoader();
      this.$store.commit("skijasi/SET_AUTH_ISSUE", {
        unauthorized: false,
      });
      this.$api.skijasiAuth
        .login({
          email: this.email,
          password: this.password,
          remember: this.rememberMe,
        })
        .then((response) => {
          this.$closeLoader();
          if (response.hasOwnProperty('data')) {
            this.$router.push({ name: "Home" });
          } else {
            this.$router.push({
              name: "AuthVerify",
              query: {
                email: this.email,
              },
            });
          }

          if (this.$statusActiveFeatureFirebase) {
            this.$messagingToken.then((tokenMessage) => {
              try {
                this.$api.skijasiFcm.saveTokenMessage(tokenMessage);
              } catch (error) {
                console.error(
                  "Errors set token firebase cloud message :",
                  error
                );
              }
            });
          }
        })
        .catch((error) => {
          this.errors = error.errors;
          this.$closeLoader();
          this.$vs.notify({
            title: this.$t("alert.danger"),
            text: error.message,
            color: "danger",
          });
        });
    },
  },
};
</script>

<style scoped>
  .dobro-doli {
    margin: 0;
    position: relative;
    font-size: inherit;
    font-weight: 700;
    font-family: inherit;
  }
  .prijavi-se {
    margin: 0;
    position: relative;
    font-size: inherit;
    font-weight: 700;
    font-family: inherit;
  }
  .prijavi-se-wrapper {
    align-self: stretch;
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    justify-content: center;
    padding: 0px 1px 28px 0px;
    font-size: 36px;
  }
  .email {
    width: 36px;
    position: relative;
    font-weight: 500;
    display: inline-block;
    min-width: 36px;
  }
  .email-label-icon {
    align-self: stretch;
    height: 50px;
    position: relative;
    max-width: 100%;
    overflow: hidden;
    flex-shrink: 0;
    z-index: 10;
  }
  .email-parent {
    align-self: stretch;
    height: 79px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    padding: 0px 0px 5px;
    box-sizing: border-box;
    gap: 7px;
    text-align: center;
  }
  

  .email-label-parent {
  align-self: stretch;
  height: 50px;
  position: relative;
}

.email-label-icon1 {
  position: absolute;
  top: 0px;
  left: 0px;
  width: 400px;
  height: 50px;
  z-index: 10;
}


  .lozinka {
    width: 52px;
    position: relative;
    font-weight: 500;
    display: inline-block;
    min-width: 52px;
  }

  .eye-off-1-icon {
    position: absolute;
    top: 13px;
    left: 358px;
    width: 24px;
    height: 24px;
    overflow: hidden;
    z-index: 1;
  }

  .lozinka-parent {
  align-self: stretch;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: flex-start;
  gap: 7px;
}

.email-container {

  width: 100%;
  height: 100%;
}

.email-input {
  position: absolute;
  top: 0;
  left: 54px;
  width: 87%;
  height: 100%;
  border: none;
  background: transparent;
  padding-left: 24px;
  font-size: 16px; /* Adjust as needed */
  overflow: hidden;
}


.password-container {
  position: relative;
  width: 100%;
  height: 100%;
}

.password-input {
  position: absolute;
  top: 0;
  left: 54px;
  width: 87%;
  height: 100%;
  border: none;
  background: transparent;
  padding-left: 24px;
  font-size: 16px;
}

.eye-icon {
  position: absolute;
  top: 50%;
  right: 10px; /* Adjust as needed */
  transform: translateY(-50%);
  cursor: pointer;
  width: 24px;
  height: 24px;
  z-index: 1;
}
  .rectangle-input {
    margin: 0;
    height: 15px;
    width: 15px;
    position: relative;
    border-radius: 3px;
    border: 1px solid #03a9f4;
    box-sizing: border-box;
  }
  .zapamti-me {
    flex: 1;
    position: relative;
    font-weight: 300;
    display: inline-block;
    min-width: 66px;
  }
  .rectangle-parent55 {
    width: 96px;
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    justify-content: flex-start;
    gap: 15px;
  }
  .zaboravljena-lozinka {
    position: relative;
    font-weight: 300;
    color: #03a9f4;
    display: inline-block;
    min-width: 115px;
  }
  .frame-parent20 {
    align-self: stretch;
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    font-size: 12px;
  }
  .frame-parent19 {
    align-self: stretch;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    padding: 0px 0px 10px;
    gap: 22px;
    text-align: center;
  }
  .login-with-email-button-child {
    height: 50px;
    width: 400px;
    position: relative;
    border-radius: 20px;
    background-color: #03a9f4;
    display: none;
    max-width: 100%;
  }
  .prijavi-se1 {
    width: 90.7px;
    position: relative;
    font-size: 16px;
    letter-spacing: -0.17px;
    font-weight: 600;
    font-family: Inter;
    color: #fff;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    z-index: 1;
  }
  .login-with-email-button {
    cursor: pointer;
    border: none;
    padding: 16px 20px 15px;
    background-color: #03a9f4;
    align-self: stretch;
    border-radius: 20px;
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    justify-content: center;
    box-sizing: border-box;
    white-space: nowrap;
    max-width: 100%;
  }
  .login-with-email-button:hover {
    background-color: #008fdb;
  }
  .kontaktiraj-administratora {
    color: #03a9f4;
  }
  .nema-raun-kontaktiraj-container {
    position: relative;
  }
  .nema-raun-kontaktiraj-admin-wrapper {
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    justify-content: flex-start;
    padding: 0px 65px 0px 66px;
  }
  .path-container-child {
    position: absolute;

bottom: 0px;
left: 0px;

max-width: 100%;
overflow: hidden;
max-height: 100%;
object-fit: contain;
  }
  .clip-path-group {
    position: absolute;
    bottom: 6%;
    left: 6%;
    width: 30%;
    height: 20%;
    z-index: 3;
  }
  .path-container {
    height: 100%;
    width: 100%;
    position: absolute;
    margin: 0 !important;

    bottom: 0px;
    left: 0px;
  }
  .sign-in-button {
    align-self: stretch;
    height: 686.5px;
    margin: 0 !important;
    position: absolute;
    width: 35%;

    bottom: 0px;
    left: 0%;
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    justify-content: flex-start;
    z-index: 2;
  }
  .vector-icon {
    position: absolute;

    top: 0px;
    right: 0px;

    max-width: 100%;
    overflow: hidden;
    max-height: 100%;
    object-fit: contain;
  }
  .g14-icon {
    position: absolute;
    top: 6%;
    right: 6%;
    width: 30%;
    height: 20%;
    z-index: 3;
  }
  .vector-parent {
    height: 100%;
    width: 100%;
    position: absolute;
    margin: 0 !important;
    top: 0px;
    right: 0px;
 
  }
  .sign-in-button1 {
    width: 35%;
    height: 686.5px;
    margin: 0 !important;
    position: absolute;
    top: 0%;
    right: 0%;

    display: flex;
    flex-direction: row;
    align-items: flex-start;
    justify-content: flex-start;
    z-index: 5;
  }
  .frame-parent18 {
    width: 496px;
    border-radius: 20px;
    background-color: rgba(255, 255, 255, 0.95);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    padding: 56px 48px;
    box-sizing: border-box;
    position: relative;
    gap: 29px;
    max-width: 100%;
  }
  .dashboardlogin-inner {
    align-self: stretch;
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    justify-content: center;
    padding: 0px 20px;
    box-sizing: border-box;
    max-width: 100%;
    text-align: left;
    font-size: 14px;
    color: #000;
    font-family: Inter;
  }
  .dashboardlogin {
    height: 100vh;

    width: 100%;
    max-width: 100%;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    padding: 61px 99px 227.5px;
    box-sizing: border-box;
    gap: 110px;
    letter-spacing: normal;
    text-align: left;
    font-size: 48px;
    color: #fff;
    font-family: Inter;
  }

  .main-container__background--auth {
  margin: 0 auto !important;
  background-size: cover;
  background-color: #fff;
  background-position: center;
  align-items: center;
  justify-content: center;
  display: flex;
  width: 100%;
  height: 100vh;
}





  @media screen and (max-width: 750px) {
    .email-label-icon1 {
  max-width: 100%;

}

    .dobro-doli {
      font-size: 38px;
    }

    .prijavi-se {
      font-size: 29px;
    }

    .dashboardlogin {
      gap: 55px 110px;
      padding-left: 49px;
      padding-right: 49px;
      box-sizing: border-box;
    }
  }
  @media screen and (max-width: 675px) {
    .frame-parent18 {
      padding: 36px 24px;
      box-sizing: border-box;
    }


    .email-input {
  position: absolute;
  top: 0;
  left: 32px;
  width: 90%;
  height: 100%;
  border: none;
  background: transparent;
  padding-left: 24px;
  font-size: 16px; /* Adjust as needed */
  overflow: hidden;
}

.password-input {
  position: absolute;
  top: 0;
  left: 32px;
  width: 90%;
  height: 100%;
  border: none;
  background: transparent;
  padding-left: 24px;
  font-size: 16px;
}

.g14-icon {
  height: 10%;
  }
  .clip-path-group { 
 height: 10%;
  }


  }
  @media screen and (max-width: 450px) {
    .email-label-icon1 {
  max-width: 100%;
}



    .dobro-doli {
      font-size: 29px;
    }

    .prijavi-se {
      font-size: 22px;
    }

    .frame-parent20 {
      flex-wrap: wrap;
      justify-content: center;
    }

    .nema-raun-kontaktiraj-admin-wrapper {
      padding-left: 20px;
      padding-right: 20px;
      box-sizing: border-box;
    }

    .dashboardlogin {
      gap: 27px 110px;
      padding-left: 20px;
      padding-right: 20px;
      box-sizing: border-box;
    }


.dashboardlogin-inner {
  padding: 0px 2px;
   }
  }
</style>
