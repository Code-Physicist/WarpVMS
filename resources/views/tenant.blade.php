@extends('layout')
@section('title')
Manage Tenants
@stop
@section('header')
Tenants
@stop
@section('sub_header')
Create and edit tenants
@stop
@section('style')
<link rel="stylesheet" href="{{ asset('css/dataTables.bs5.css') }}" />
<link rel="stylesheet" href="{{ asset('css/dataTables.bs5-custom.css') }}" />
@stop
@section('content')
  <div v-show="active_ui === 1" class="row" style="display:none;">
    <div class="col-12">
      <div class="card mb-3">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center justify-content-start">
              <button @click="show_create" class="btn btn-danger btn-sm mx-2"><span class="icon-add"></span></button>
              New Tenant
            </div>
            <div class="d-flex align-items-center justify-content-end">
              <div class="card-title text-nowrap">Status</div>
              <div>&nbsp;&nbsp;</div>
              <select @change="change_filter" v-model="filter.status" class="form-select form-select-sm">
                      <option value="2">All</option>
                      <option value="1">Active</option>
                      <option value="0">Disabled</option>
              </select>
            </div>
          </div>
          <hr/>
          <div class="table-responsive">
              <table class="table table-striped" style="width:100%" id="data-table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>
                      <div class="d-flex align-items-center">
                        <span class="icon-user me-2 fs-4"></span>
                        Name
                      </div>
                    </th>
                    <th>
                      <div class="d-flex align-items-center">
                        <span class="icon-mail me-2 fs-4"></span>
                        Email
                      </div>
                    </th>
                    <th>
                      <div class="d-flex align-items-center">
                        <span class="icon-business me-2 fs-4"></span>
                        Department
                      </div>
                    </th>
                    <th>
                      <div class="d-flex justify-content-center align-items-center">
                        <span class="icon-lightbulb me-2 fs-4"></span>
                        Status
                      </div>
                    </th>
                    <th>
                      <div class="d-flex justify-content-center align-items-center">
                        <span class="icon-settings me-2 fs-4"></span>
                        Actions
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div> 
        </div>
      </div>
    </div>
  </div>
  <div v-show="active_ui === 2" class="row" style="display:none;">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title">Create Tenant</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="offset-md-4 col-md-4">
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Department *</label>
                  <select v-model="tenant.dept_id" class="form-select">
                      <option value="-1">Please select</option>
                      <option v-for="d in lv1_depts" :value="d.dept_id">
                        {d.full_name}
                      </option>
                  </select>
                  <div v-if="tenant_dept_msg !== ''" class="ms-1 mt-1 text-danger">{tenant_dept_msg}</div>
                </div>
              </div>
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Email *</label>
                  <input type="text" class="form-control" v-model.trim="tenant.email"/>
                  <div v-if="tenant_email_msg !== ''" class="ms-1 mt-1 text-danger">{tenant_email_msg}</div>
                </div>
              </div>
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Name *</label>
                  <input type="text" class="form-control" v-model.trim="tenant.name"/>
                  <div v-if="tenant_name_msg !== ''" class="ms-1 mt-1 text-danger">{tenant_name_msg}</div>
                </div>
              </div>
              <div v-show="tenant_form_message !== ''">
						    <div class="alert alert-primary d-flex align-items-center">
                    <i class="icon-alert-triangle fs-2 me-2 lh-1"></i>
                    { tenant_form_message }
                </div>
					    </div>
              <div class="my-3 d-flex align-items-end justify-content-center">
                <button type="button" @click="active_ui = 1" class="btn btn-outline-secondary mx-2">Cancel</button> <button type="button" @click="submit" class="btn btn-danger mx-2">Submit</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal rounded-3 fade" role="dialog" data-bs-backdrop="static" ref="edb_modal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-body px-4 pt-4 pb-4 text-center">
            <h5 class="text-primary">{ edb_status === 0 ? 'Disable' : 'Enable' } this tenant?</h5>
              <div class="mt-4 mb-2">
                  {edb_tenant_name}
              </div>
              <div v-show="edb_modal_message !== ''" class="mb-0">
						    <div class="alert alert-primary d-flex justify-content-center align-items-center mb-0 p-2">
                    <i class="icon-alert-triangle fs-2 me-2 lh-1"></i>
                    { edb_modal_message }
                </div>
					    </div>
          </div>
          <div class="modal-footer flex-nowrap p-0">
            <button type="button" @click="submit_edb" class="btn text-primary fs-6 col-6 m-0 border-end">
              <strong>{ edb_status === 0 ? 'Disable' : 'Enable' }</strong>
            </button>
            <button type="button" class="btn  fs-6 col-6 m-0" data-bs-dismiss="modal">
              Cancel
            </button>
          </div>
        </div>
      </div>
  </div>
@stop
@section('script')
<script src="{{ asset('js/dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
<script>

function show_edit(id) {
  app.show_edit(id);
}
function show_edb(id, status) {
  app.show_edb(id, status);
}

window.app = Vue.createApp({
    data() {
      return {
        active_ui: 1,
        lv1_depts: [],
        filter: {
          status: 2
        },
        tenant_table: null,
        tenants:[],
        tenant:{
            id: 0,
            email: "",
            name: "",
            dept_id: -1,
        },
        tenant0: null,
        tenant_form_message: "",
        tenant_dept_msg: "",
        tenant_name_msg: "",
        tenant_email_msg: "",
        edb_modal: null,
        edb_tenant_id: 0,
        edb_tenant_name: "",
        edb_status: 0,
        edb_modal_message:"",
      }
    },
    mounted() {
      this.tenant0 = { ...this.tenant };
      this.init_ui();
      this.edb_modal = new bootstrap.Modal(this.$refs.edb_modal, { keyboard: false });
    },
    methods: {
      async init_ui() {
        try {
          await this.get_lv1_depts();
          this.init_data_table();
        }
        catch(error) {
          console.log(error);
        }
      },
      init_data_table () {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        this.tenant_table = $("#data-table").DataTable({
          processing: true,
          serverSide: true,
          searchDelay: 500,
          ajax: {
            url: axios.defaults.baseURL + "/admin/get_tenants",
            type: "POST",
            "headers": {
              "X-CSRF-TOKEN": csrfToken
            },
            data: this.get_status,
            dataSrc: this.post_process,
            error: function(xhr, error, thrown) {
              // Handle errors here
              console.log('AJAX Error: ', error); // Log error to console
            
              // Optional: Handle specific errors
              if (xhr.status === 401) {
                window.location.href = "{{url('/admin/logout')}}";
              }
              if (xhr.status === 404) {
                alert('Server not found (404).');
              } else if (xhr.status === 500) {
                alert('Server error (500).');
              }
              // You can add more specific error handling here
            }
          },
          lengthMenu: [[10, 25],[10, 25]],
          searching: true,
          columns: [
            { data: "id", orderable: true},
            { data: "name", orderable: true},
            { data: "email", orderable: true},
            { data: "full_name", orderable: true},
            { data: "status", orderable: false},
            { data: "action", orderable: false}
          ]
        });

      },
      clear_form_message() {
        this.tenant_form_message = "";
        this.tenant_dept_msg = "";
        this.tenant_name_msg = "";
        this.tenant_email_msg = "";
      },
      show_create() {
        this.clear_form_message();
        MyApp.copy_vals(this.tenant0, this.tenant);
        this.active_ui = 2;
      },
      show_edit(id) {
        let tenant = null;
        for(let i = 0; i < this.tenants.length; i++)
        {
          if(this.tenants[i].id == id)
          {
            tenant = this.tenants[i];
            break;
          }
        }
        this.clear_form_message();
        MyApp.copy_vals(tenant, this.tenant);
        this.active_ui = 2;
      },
      show_edb(id, status) {
        let tenant = null;
        for(let i = 0; i < this.tenants.length; i++)
        {
          if(this.tenants[i].id == id)
          {
            tenant = this.tenants[i];
            break;
          }
        }
        this.edb_tenant_id = tenant.id;
        this.edb_tenant_name = tenant.name;
        this.edb_status = status;
        this.edb_modal.show();
      },
      async submit() {
        this.clear_form_message();

        let is_valid = true;
        if (this.tenant.dept_id === -1) {
          this.tenant_dept_msg = "Please select Department";
          is_valid = false;
        }
        if (this.tenant.name === "") {
          this.tenant_name_msg = "Name is blank";
          is_valid = false;
        }
        if(this.tenant.email === "")
        {
          this.tenant_email_msg = "Email is blank";
					is_valid = false;
        }
        else if (!validator.isEmail(this.tenant.email))
				{
					this.tenant_email_msg = "Incorrect Email format";
					is_valid = false;
				}

        if(!is_valid) return;

        let url = (this.tenant.id === 0) ? "/admin/create_tenant" : "/admin/update_tenant";
        try {
          const response = await axios.post(url, this.tenant);
          if(response.data.status === "T")
          {
            let admin = response.data.admin;
            let admin_url = `${axios.defaults.baseURL}/admin/login`;

            //Not wait until email sent finished
            axios.post("/admin/send_admin_email", {admin: admin, admin_url: admin_url});

            this.tenant_table.ajax.reload(null, false);
            this.active_ui = 1;
          }
          else if(response.data.status === "I")
          {
            //Force log out
            this.tenant_form_message = "Token expired. You will be logged out soon...";
            setTimeout(function() {window.location.href = "{{url('/admin/logout')}}";}, 3000);
          }
          else
          {
            this.tenant_form_message = "Invalid Data Submission";
          }
        }
        catch(error) {
          console.log(error);
          this.tenant_form_message = "Server error. Please try again later";
        }
      },
      async submit_edb() {
        try {
          const response = await axios.post("/admin/update_tenant_edb", {id:this.edb_tenant_id, status:this.edb_status});
          if(response.data.status === "I")
          {
            //Force log out
            this.edb_modal_message = "Token expired. You will be logged out soon...";
            setTimeout(function() {window.location.href = "{{url('/admin/logout')}}";}, 3000);
          }
          else
          {
            this.edb_modal.hide();
            //Make sure not to change datatable configurations
            this.tenant_table.ajax.reload(null, false);
            this.active_ui = 1;
          }
        }
        catch(error) {
          console.log(error);
          this.edb_modal_message = "Server error. Please try again later.";
        }
      },
      async get_lv1_depts() {
        const response = await axios.post("/admin/get_lv1_depts");
        this.lv1_depts = response.data.data_list;
      },
      get_status(d) {
        d.status = this.filter.status;
      },
      post_process(json) {
        this.tenants = json.aaData;
        // Modify the json data here before DataTable uses it
        for (var i = 0; i < this.tenants.length; i++) {
          let tenant = this.tenants[i];
          if(tenant.is_active == 1) tenant.status = "<div class='text-center'><span class='badge bg-success'>Active</span></div>";
          else tenant.status = "<div class='text-center'><span class='badge bg-secondary'>Disabled</span></div>";
          
          tenant.action = "<div class='d-flex justify-content-center align-items-center'>";
          tenant.action += "<a class='cursor-pointer' data-bs-toggle='dropdown'><h5 class='mb-0'><i class='icon-more-vertical'></i></h5></a>";
          tenant.action += "<ul class='dropdown-menu shadow-sm dropdown-menu-mini'>";
          tenant.action += `<li><a class='dropdown-item cursor-pointer' onclick='show_edit(${tenant.id})'><span class='icon-edit fs-5'></span> Edit</a></li>`;
          if(tenant.is_active == 1)
            tenant.action += `<a class="dropdown-item cursor-pointer" onclick="show_edb(${tenant.id}, 0)"><span class="icon-x-square fs-5"></span> Disable</a></li>`;
          else
            tenant.action += `<a class="dropdown-item cursor-pointer" onclick="show_edb(${tenant.id}, 1)"><span class="icon-check-square fs-5"></span> Enable</a></li>`;

          tenant.action += "</ul>";
          tenant.action += "</div>";
        }
        return json.aaData;
      },
      change_filter() {
        this.tenant_table.ajax.reload();
      }
    },
    delimiters: ["{","}"]
}).mount('#app');
</script>
@stop