<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-base-url" content="{{ url('') }}" />
    <title>BTS Visionary Park</title>

    <!-- *************
			************ CSS Files *************
		************* -->
    <!-- Icomoon Font Icons css -->
    <link rel="stylesheet" href="{{ mix('fonts/icomoon/style.css') }}" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ mix('css/main.min.css') }}" />

    <link rel="stylesheet" href="{{ mix('css/app.css') }}" />
  </head>

  <body style="background-color:#EEEBE8;">
    <div class="d-flex vh-100">
      <div class="flex-grow-55 vh-100">
        <div class="xx-bts-logo">
          <img src="{{ mix('images/BTSVISION_Logo_DarkBlue.svg') }}" alt="">
        </div>
      </div>
      <div class="d-flex flex-grow-45 vh-100" style="background-color:#1a2752">
          <div id="app" class="bg-white x-login-form">
            <form v-show="active_ui === 1">
              <div class="rounded-3 p-4">
                <div class="login-form">
                  <h4 class="my-4">Login</h4>
                  <div class="mb-3">
                    <label class="form-label">Your Email <span class="text-danger">*</span></label>
                    <input type="text" v-model.trim="user.email" class="form-control" autocomplete="autocomplete"
                    placeholder="Enter your email" />
                    <div v-if="email_msg !== ''" class="ms-1 mt-1 text-danger">{email_msg}</div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Your Password <span class="text-danger">*</span></label>
                    <input type="password" v-model.trim="user.pass" class="form-control" placeholder="Enter password" />
                    <div v-if="password_msg !== ''" class="ms-1 mt-1 text-danger">{pass_msg}</div>
                  </div>
                  <div class="d-flex align-items-center justify-content-end">
                    <a @click="change_ui(2)" class="text-primary text-decoration-underline cursor-pointer">Lost password?</a>
                  </div>
                  <div class="d-grid py-3 mt-3">
                    <button type="button" @click="submit_login" class="btn btn-lg btn-primary">
                      LOGIN
                    </button>
                  </div>
                </div>
              </div>
            </form>
            <form v-show="active_ui === 2">
              <div class="rounded-3 p-4">
                <div class="login-form">
                  <h4 class="my-4">Reset Password</h4>
                  <div class="mb-3">
                    <label class="form-label">Your Email <span class="text-danger">*</span></label>
                    <input type="text" v-model.trim="user.email" class="form-control" autocomplete="autocomplete"
                    placeholder="Enter your email" />
                    <div v-if="email_msg !== ''" class="ms-1 mt-1 text-danger">{email_msg}</div>
                  </div>
                  <div class="d-flex align-items-center justify-content-end">
                    <a @click="change_ui(1)" class="text-primary text-decoration-underline cursor-pointer">To Login</a>
                  </div>
                  <div class="d-grid py-3 mt-3">
                    <button type="button" @click="submit_reset" class="btn btn-lg btn-primary">
                      SEND
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div> 
      </div>
    </div>
    <!-- Container end -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/validator.min.js') }}"></script>
    <script src="{{ asset('js/vue.global.prod.js') }}"></script>
    <script>
      const { createApp } = Vue;

      // Create Vue App
      const app = createApp({
          components: {},
          data() {
            return {
              active_ui: 1, //1 => login, 2 => forgot password
              loadingDialog: false,
              user: {
                email: "",
                pass: "",
              },
              email_msg: "",
              pass_msg: ""
            };
          },
          methods: {
            async submit_login() {
              this.email_msg = "";
              this.pass_msg = "";

              let is_valid = true;
              if (this.user.email === "") {
                this.email_msg = "Email is blank";
                is_valid = false;
              }
              else if (!validator.isEmail(this.user.email)) {
                this.email_msg = "Incorrect Email format";
                is_valid = false;
              }
              if (this.user.pass === "") {
                this.pass_msg = "Password is blank";
                is_valid = false;
              }

              if(!is_valid) return;

              try {
                const response = await axios.post("/admin/login", this.user);
                if(response.data.status == "T") {
                  window.location.href = "/admin/dashboard";
                }
                else {
                  console.log(response.data);
                }
              }
              catch(error) {
                console.log(error);
              }

            },
            async submit_reset() {
              this.email_msg = "";

              let is_valid = true;
              if (this.user.email === "") {
                this.email_msg = "Email is blank";
                is_valid = false;
              }
              else if (!validator.isEmail(this.user.email)) {
                this.email_msg = "Incorrect Email format";
                is_valid = false;
              }

              if(!is_valid) return;

              try {
                const response = await axios.post(url, "/user/reset");
                if(response.data.status == "T") {
                }
                else {
                  console.log(response.data.err_message);
                }
              }
              catch(error) {
                console.log(error);
              }
            },
            change_ui(ui) {
              this.active_ui = ui;
              this.user.email = "";
              this.user.password = "";
              this.email_msg = "";
              this.password_msg = "";
            },
            refresh() {
              window.location.href = axios.defaults.baseURL + "/admin";
            },
          },
          delimiters: ["{","}"]
      });

      app.mount("#app");
    </script>
  </body>
</html>