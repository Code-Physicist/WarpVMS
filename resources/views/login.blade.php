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
    <link rel="stylesheet" href="{{ asset('fonts/icomoon/style.css') }}" />

    <!-- Aldrich -->
    <link rel="stylesheet" href="{{ asset('fonts/aldrich/style.css') }}" />

    <!-- Poppins -->
    <link rel="stylesheet" href="{{ asset('fonts/poppins/style.css') }}" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('css/main.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
  </head>

  <body style="background-color:#1a2752;">
  <div id="app" class="container">
      <div class="row justify-content-center">
        <div class="col-xl-4 col-lg-5 col-sm-6 col-12">
        <form class="my-5">
              <div class="bg-white rounded-3 p-4">
                <div class="login-form x-login-form">
                  <a class="mb-4 d-flex align-items-center justify-content-between">
                    <img src="{{ asset('images/BTSVISION_Logo_DarkBlue.svg') }}" class="img-fluid login-logo" alt="BTS Visionary Park" />
                    <h3 v-show="true" class="h4-login-form my-0 pe-2" style="display:none;">{active_ui === 1? 'Login':'Reset'}</h3>
                  </a>
                  <div v-show="active_ui === 1" style="display:none;">
                  <div class="mb-3">
                    <label class="form-label">Your Email <span class="text-danger">*</span></label>
                    <input type="text" v-model.trim="user.email" class="form-control" autocomplete="autocomplete"
                    placeholder="Enter your email" />
                    <div v-if="email_msg !== ''" class="ms-1 mt-1 text-danger">{email_msg}</div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Your Password <span class="text-danger">*</span></label>
                    <input type="password" v-model.trim="user.pass" class="form-control" placeholder="Enter password" />
                    <div v-if="pass_msg !== ''" class="ms-1 mt-1 text-danger">{pass_msg}</div>
                  </div>
                  <div class="d-flex align-items-center justify-content-end mb-4">
                    <a @click="change_ui(2)" class="text-primary text-decoration-underline cursor-pointer">Lost password?</a>
                  </div>
                  <div v-show="login_form_message !== ''">
						        <div class="alert alert-primary d-flex align-items-center">
                      <i class="icon-alert-triangle fs-2 me-2 lh-1"></i>
                      { login_form_message }
                    </div>
					        </div>
                  <div class="d-grid py-3">
                    <button type="button" @click="submit_login" class="btn btn-lg btn-primary">
                      LOGIN
                    </button>
                  </div>
                  </div>

                  <div v-show="active_ui === 2" style="display:none;">
                  <div class="mb-3">
                    <label class="form-label">Your Email <span class="text-danger">*</span></label>
                    <input type="text" v-model.trim="user.email" class="form-control" autocomplete="autocomplete"
                    placeholder="Enter your email" />
                    <div v-if="email_msg !== ''" class="ms-1 mt-1 text-danger">{email_msg}</div>
                  </div>
                  <div class="d-flex align-items-center justify-content-end">
                    <a @click="change_ui(1)" class="text-primary text-decoration-underline cursor-pointer">To Login</a>
                  </div>
                  <div v-show="login_form_message !== ''" class="mt-4">
						        <div class="alert alert-primary d-flex align-items-center">
                      <i class="icon-alert-triangle fs-2 me-2 lh-1"></i>
                      { login_form_message }
                    </div>
					        </div>
                  <div class="d-grid py-3 mt-3">
                    <button type="button" @click="submit_reset" class="btn btn-lg btn-primary">
                      SEND
                    </button>
                  </div>
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
              pass_msg: "",
              login_form_message: "",
            };
          },
          methods: {
            async submit_login() {
              this.email_msg = "";
              this.pass_msg = "";
              this.login_form_message = "";

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
                  window.location.href = "{{url('/admin/dashboard')}}";
                }
                else {
                  this.login_form_message = "Invalid Username or Password";
                }
              }
              catch(error) {
                console.log(error);
              }

            },
            async submit_reset() {
              alert("Under Construction");
              return;
              
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
                const response = await axios.post(url, "/admin/reset");
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
              this.pass_msg = "";
              this.login_form_message = "";
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