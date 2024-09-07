<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-base-url" content="{{ url('') }}" />
    <title>Change Password</title>

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
        <form class="my-5" v-cloak>
              <div class="bg-white rounded-3 p-4">
                <div class="login-form x-login-form">
                  <a class="mb-4 d-flex align-items-center justify-content-between">
                    <img src="{{ asset('images/BTSVISION_Logo_DarkBlue.svg') }}" class="img-fluid login-logo" alt="BTS Visionary Park" />
                    <div><h5 class="h4-login-form my-0 pe-2">Change</h5><h5 class="h4-login-form my-0 pe-2">Password</h5></div>
                  </a>

                  <div class="mb-3">
                    <label class="form-label">New password <span class="text-danger">*</span></label>
                    <input type="password" v-model.trim="user.pass1" class="form-control" placeholder="Password" />
                    <div v-if="pass1_msg !== ''" class="ms-1 mt-1 text-danger">{pass1_msg}</div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Re-enter password <span class="text-danger">*</span></label>
                    <input type="password" v-model.trim="user.pass2" class="form-control" placeholder="Password" />
                    <div v-if="pass2_msg !== ''" class="ms-1 mt-1 text-danger">{pass2_msg}</div>
                  </div>
                  <div v-show="form_message !== ''" class="mt-4">
						        <div class="alert alert-primary d-flex align-items-center">
                      <i class="icon-alert-triangle fs-2 me-2 lh-1"></i>
                      { form_message }
                    </div>
					        </div>
                  <div v-show="form_success" class="mt-4">
						        <div class="alert alert-success d-flex align-items-center">
                      Password changed successfully. &nbsp;<a class="text-success text-decoration-underline cursor-pointer" onclick="window.history.go(-1);">Go back</a>
                    </div>
					        </div>
                  <div class="d-grid py-3 mt-3">
                    <button type="button" @click="submit" class="btn btn-lg btn-primary">SEND</button>
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
              user: {
                pass1: "",
                pass2: "",
              },
              pass1_msg: "",
              pass2_msg: "",
              form_message: "",
              form_success: false
            };
          },
          methods: {
            async submit() {
              this.form_message = "";
              this.pass1_msg = "";
              this.pass2_msg = "";
              this.form_success = false;

              let is_valid = true;
              if (this.user.pass1 === "") {
                this.pass1_msg = "Password can't be blank";
                is_valid = false;
              }

              if (this.user.pass2 === "") {
                this.pass2_msg = "Password can't be blank";
                is_valid = false;
              }

              if (this.user.pass1 !== this.user.pass2) {
                this.pass2_msg = "Password is not the same";
                is_valid = false;
              }
              
              if(!is_valid)
                return;

              try {
                response = await axios.post("/admin/change_passwrd", this.user);

                let data = response.data;
                if(data.status === "I") {
                  //Force log out
                  this.form_message = "Token expired. You will be logged out soon...";
                  setTimeout(function() {window.location.href = "{{url('/admin/logout')}}";}, 3000);
                }
                else if(data.status === "T") {
                  this.form_success = true;
                  this.user.pass1 = "";
                  this.user.pass2 = "";
                }
                else
                  this.form_message = "Server error. Please try again later";
              }
              catch(error) {
                console.log(error);
                this.form_message = "Server error. Please try again later";
              }          
                  
            },
          },
          delimiters: ["{","}"]
      });

      app.mount("#app");
    </script>
  </body>
</html>