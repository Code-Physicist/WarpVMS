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
<div id="app">
  <div v-show="active_ui === 1" class="row" style="display:none;">
    <div class="col-12">
      <div class="card mb-3">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center justify-content-start">
              <div class="card-title">Total {operators.length} Operators</div>
              <button @click="show_create" class="btn btn-danger btn-sm mx-2"><span class="icon-add"></span> Create&nbsp;</button>
            </div>
            <div class="d-flex align-items-center justify-content-end">
              <div class="card-title text-nowrap">Status</div>
              <div>&nbsp;&nbsp;</div>
              <select @change="change_filter" v-model="filter.status" class="form-select">
                      <option value="2">All</option>
                      <option value="1">Active</option>
                      <option value="0">Disabled</option>
              </select>
            </div>
          </div>
          <hr/>
          <div class="table-responsive">
              <table class="table table-striped" id="data-table">
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
                      <div class="d-flex justify-content-center align-items-center">
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
                  <tr v-for="(operator, index) in operators">
                    <td>{index+1}.</td>
                    <td>{operator.name}</td>
                    <td>{operator.email}</td>
                    <td class="text-center">{admin_type_dict[operator.admin_level_id]}</td>
                    <td>{operator.full_name}</td>
                    <td class="text-center">
                        <span v-if="operator.is_active === '1'" class="badge bg-success">Active</span>
                        <span v-else class="badge bg-secondary">Disabled</span>
                    </td>
                    <td>
                      <div class="d-flex justify-content-center align-items-center">
                        <a class="cursor-pointer" data-bs-toggle="dropdown"><h5><span class="icon-more-vertical"></span></h5></a>
                        <ul class="dropdown-menu shadow-sm dropdown-menu-mini">
                          <li><a class="dropdown-item cursor-pointer" @click="show_edit(operator)"><span class="icon-edit fs-5"></span> Edit</a></li>
                          <li v-if="operator.is_active === '1'"><a class="dropdown-item cursor-pointer" @click="show_edb(operator, 0)"><span class="icon-x-square fs-5"></span> Disable</a></li>
                          <li v-else><a class="dropdown-item cursor-pointer" @click="show_edb(operator, 1)"><span class="icon-check-square fs-5"></span> Enable</a></li>
                        </ul>
                      </div>
                    </td>
                  </tr>
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
</div>
@stop
@section('script')
<script src="{{ asset('js/dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
<script>
const { createApp } = Vue;
createApp({
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
          await this.get_operators();
        }
        catch(error) {
          console.log(error);
        }
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
      show_edit(operator) {
        this.clear_form_message();
        MyApp.copy_vals(operator, this.operator);
        this.active_ui = 2;
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
            
            this.get_operators();
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
      show_edb(operator, status) {
        this.edb_operator_id = operator.id;
        this.edb_operator_name = operator.name;
        this.edb_status = status;
        this.edb_modal.show();
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
            this.get_operators();
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
      },
      async get_operators() {
        $('#data-table').DataTable().destroy();
        this.operators = [];
        const response = await axios.post("/admin/get_operators", this.filter);
        this.operators = response.data.data_list;
        Vue.nextTick(() => {
          $('#data-table').DataTable();
        });
      },
      change_filter() {
        this.get_operators();
      }
    },
    delimiters: ["{","}"]
}).mount('#app');
</script>
@stop