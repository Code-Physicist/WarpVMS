@extends('layout')
@section('title')
Manage Operators
@stop
@section('header')
Operators
@stop
@section('sub_header')
Create and edit operators
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
              New Operator
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
                        <span class="icon-published_with_changes me-2 fs-4"></span>
                        Type
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
          <h5 class="card-title">Create Operator</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="offset-md-4 col-md-4">
@if($admin_level_id != '2')
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Department *</label>
                  <select v-model="operator.dept_id" class="form-select">
                      <option value="-1">Please select</option>
                      <option v-for="d in depts" :value="d.dept_id">
                        {d.full_name}
                      </option>
                  </select>
                  <div v-if="operator_dept_msg !== ''" class="ms-1 mt-1 text-danger">{operator_dept_msg}</div>
                </div>
              </div>
@endif
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Type *</label>
                  <select v-model="operator.admin_level_id" class="form-select">
                      <option value="0">Please select</option>
                      <option v-for="l in admin_levels" :value="l.id">
                        {l.name}
                      </option>
                  </select>
                  <div v-if="operator_type_msg !== ''" class="ms-1 mt-1 text-danger">{operator_type_msg}</div>
                </div>
              </div>
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Email *</label>
                  <input type="text" class="form-control" v-model.trim="operator.email"/>
                  <div v-if="operator_email_msg !== ''" class="ms-1 mt-1 text-danger">{operator_email_msg}</div>
                </div>
              </div>
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Name *</label>
                  <input type="text" class="form-control" v-model.trim="operator.name"/>
                  <div v-if="operator_name_msg !== ''" class="ms-1 mt-1 text-danger">{operator_name_msg}</div>
                </div>
              </div>
              <div v-show="operator_form_message !== ''">
						    <div class="alert alert-primary d-flex align-items-center">
                    <i class="icon-alert-triangle fs-2 me-2 lh-1"></i>
                    { operator_form_message }
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
            <h5 class="text-primary">{ edb_status === 0 ? 'Disable' : 'Enable' } this operator?</h5>
              <div class="mt-4 mb-2">
                  {edb_operator_name}
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
        admin_levels: [],
        admin_type_dict: {
          2:"Building Owner",
          3:"Building Operator",
          4:"Tenant",
          5:"Operator",
        },
        depts: [],
        dept_dict:{},
        filter: {
          status: 2
        },
        operator_table: null,
        operators:[],
        operator:{
          id: 0,
          name: "",
          email: "",
          admin_level_id: 0,
          dept_id: -1,
        },
        operator0: null,
        operator_form_message: "",
        operator_dept_msg: "",
        operator_name_msg: "",
        operator_email_msg: "",
        operator_type_msg: "",
        edb_modal: null,
        edb_operator_id: 0,
        edb_operator_name: "",
        edb_status: 0,
        edb_modal_message:"",
      }
    },
    mounted() {
@if($admin_level_id == '2')
      this.admin_levels = [
        {"id":"2", "name":"Building Owner"},
        {"id":"3", "name":"Building Operator"}
      ];
@elseif($admin_level_id == '4')
      this.admin_levels = [
        {"id":"4", "name":"Tenant"},
        {"id":"5", "name":"Tenant Operator"}
      ];
@endif
      this.operator0 = { ...this.operator };
      this.init_ui();
      this.edb_modal = new bootstrap.Modal(this.$refs.edb_modal, { keyboard: false });
    },
    methods: {
      async init_ui() {
        try {
          await this.get_depts();
          this.init_data_table();
        }
        catch(error) {
          console.log(error);
        }
      },
      init_data_table () {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        this.operator_table = $("#data-table").DataTable({
          processing: true,
          serverSide: true,
          searchDelay: 500,
          ajax: {
            url: axios.defaults.baseURL + "/admin/get_operators",
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
            { data: "type", orderable: false},
            { data: "full_name", orderable: true},
            { data: "status", orderable: false},
            { data: "action", orderable: false}
          ]
        });

      },
      get_status(d) {
        d.status = this.filter.status;
      },
      post_process(json) {
        this.operators = json.aaData;
        // Modify the json data here before DataTable uses it
        for (var i = 0; i < this.operators.length; i++) {
          let operator = this.operators[i];

          operator.type = `<div>${this.admin_type_dict[operator.admin_level_id]}</div>`;
          if(operator.is_active == 1) operator.status = "<div class='text-center'><span class='badge bg-success'>Active</span></div>";
          else operator.status = "<div class='text-center'><span class='badge bg-secondary'>Disabled</span></div>";
          
          operator.action = "<div class='d-flex justify-content-center align-items-center'>";
          operator.action += "<a class='cursor-pointer' data-bs-toggle='dropdown'><h5 class='mb-0'><i class='icon-more-vertical'></i></h5></a>";
          operator.action += "<ul class='dropdown-menu shadow-sm dropdown-menu-mini'>";
          operator.action += `<li><a class='dropdown-item cursor-pointer' onclick='show_edit(${operator.id})'><span class='icon-edit fs-5'></span> Edit</a></li>`;
          if(operator.is_active == 1)
            operator.action += `<a class="dropdown-item cursor-pointer" onclick="show_edb(${operator.id}, 0)"><span class="icon-x-square fs-5"></span> Disable</a></li>`;
          else
            operator.action += `<a class="dropdown-item cursor-pointer" onclick="show_edb(${operator.id}, 1)"><span class="icon-check-square fs-5"></span> Enable</a></li>`;

          operator.action += "</ul>";
          operator.action += "</div>";
        }
        return json.aaData;
      },
      change_filter() {
        this.operator_table.ajax.reload();
      },
      clear_form_message() {
        this.operator_form_message = "";
        this.operator_dept_msg = "";
        this.operator_name_msg = "";
        this.operator_email_msg = "";
        this.operator_type_msg = "";
      },
      show_create() {
        this.clear_form_message();
        MyApp.copy_vals(this.operator0, this.operator);
        this.active_ui = 2;
      },
      show_edit(id) {
        let operator = null;
        for(let i = 0; i < this.operators.length; i++)
        {
          if(this.operators[i].id == id)
          {
            operator = this.operators[i];
            break;
          }
        }
        this.clear_form_message();
        MyApp.copy_vals(operator, this.operator);
        this.active_ui = 2;
      },
      show_edb(id, status) {
        let operator = null;
        for(let i = 0; i < this.operators.length; i++)
        {
          if(this.operators[i].id == id)
          {
            operator = this.operators[i];
            break;
          }
        }
        this.edb_operator_id = operator.id;
        this.edb_operator_name = operator.name;
        this.edb_status = status;
        this.edb_modal.show();
      },
      async submit() {
        this.clear_form_message();

        let is_valid = true;
@if($admin_level_id == '2')
        this.operator.dept_id = 0;
@endif
@if($admin_level_id == '4')
        if (this.operator.dept_id == "-1") {
          this.operator_dept_msg = "Please select Department";
          is_valid = false;
        }
@endif
        if (this.operator.admin_level_id == "0") {
          this.operator_type_msg = "Please select Type";
          is_valid = false;
        }
        if (this.operator.name === "") {
          this.operator_name_msg = "Name is blank";
          is_valid = false;
        }
        if(this.operator.email === "")
        {
          this.operator_email_msg = "Email is blank";
					is_valid = false;
        }
        else if (!validator.isEmail(this.operator.email))
				{
					this.operator_email_msg = "Email format is not correct";
					is_valid = false;
				}

        if(!is_valid) return;

        console.log(this.operator);

        let url = (this.operator.id == "0") ? "/admin/create_operator" : "/admin/update_operator";
        try {
          const response = await axios.post(url, this.operator);
          if(response.data.status === "T")
          {
            let admin = response.data.admin;
            let admin_url = `${axios.defaults.baseURL}/admin/login`;

            //Not wait until email sent finished
            axios.post("/admin/send_admin_email", {admin: admin, admin_url: admin_url});
            
            this.operator_table.ajax.reload(null, false);
            this.active_ui = 1;
          }
          else if(response.data.status === "I")
          {
            //Force log out
            this.operator_form_message = "Token expired. You will be logged out soon...";
            setTimeout(function() {window.location.href = "{{url('/admin/logout')}}";}, 3000);
          }
          else
          {
            this.operator_form_message = "Invalid Data Submission";
          }
        }
        catch(error) {
          console.log(error);
          this.operator_form_message = "Server error. Please try again later";
        }
      },
      async submit_edb() {
        try {
          const response = await axios.post("/admin/update_operator_edb", {id:this.edb_operator_id, status:this.edb_status});
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
            this.operator_table.ajax.reload(null, false);
            this.active_ui = 1;
          }
        }
        catch(error) {
          console.log(error);
          this.edb_modal_message = "Server error. Please try again later.";
        }
      },
      async get_depts() {
        const response = await axios.post("/admin/get_operator_depts");
        this.depts = response.data.data_list;
      }
    },
    delimiters: ["{","}"]
}).mount('#app');
</script>
@stop