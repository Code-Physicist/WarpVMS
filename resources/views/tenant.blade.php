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
<div id="app">
  <div v-show="active_ui === 1" class="row">
    <div class="col-12">
      <div class="card mb-3">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center justify-content-start">
              <div class="card-title">Total {tenants.length} Tenants</div>
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
                        <span class="icon-published_with_changes me-2 fs-4"></span>
                        Name
                      </div>
                    </th>
                    <th>
                      <div class="d-flex align-items-center">
                        <span class="icon-add_task me-2 fs-4"></span>
                        Email
                      </div>
                    </th>
                    <th>
                      <div class="d-flex align-items-center">
                        <span class="icon-published_with_changes me-2 fs-4"></span>
                        Department
                      </div>
                    </th>
                    <th>
                      <div class="d-flex justify-content-center align-items-center">
                        <span class="icon-settings me-2 fs-4"></span>
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
                  <tr v-for="(tenant, index) in tenants">
                    <td>{index+1}.</td>
                    <td>{tenant.name}</td>
                    <td>{tenant.email}</td>
                    <td>{tenant.full_name}</td>
                    <td class="text-center">
                        <span v-if="tenant.is_active === '1'" class="badge bg-success">Active</span>
                        <span v-else class="badge bg-secondary">Disabled</span>
                    </td>
                    <td>
                      <div class="d-flex justify-content-center align-items-center">
                        <a class="cursor-pointer" data-bs-toggle="dropdown"><h5><span class="icon-more-vertical"></span></h5></a>
                        <ul class="dropdown-menu shadow-sm dropdown-menu-mini">
                          <li><a class="dropdown-item cursor-pointer" @click="show_edit(tenant)"><span class="icon-edit fs-5"></span> Edit</a></li>
                          <li v-if="tenant.is_active === '1'"><a class="dropdown-item cursor-pointer" @click="show_edb(tenant, 0)"><span class="icon-x-square fs-5"></span> Disable</a></li>
                          <li v-else><a class="dropdown-item cursor-pointer" @click="show_edb(tenant, 1)"><span class="icon-check-square fs-5"></span> Enable</a></li>
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
  <div v-show="active_ui === 2" class="row">
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
        lv1_depts: [],
        filter: {
          status: 2
        },
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
          await this.get_tenants();
        }
        catch(error) {
          console.log(error);
        }
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
      show_edit(tenant) {
        this.clear_form_message();
        MyApp.copy_vals(tenant, this.tenant);
        this.active_ui = 2;
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
            this.get_tenants();
            this.active_ui = 1;
          }
          else if(response.data.status === "I")
          {
            //Force log out
            this.tenant_form_message = "Token expired. You will be logged out soon...";
            setTimeout(function() {window.location.href = "/admin/logout";}, 3000);
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
      show_edb(tenant, status) {
        this.edb_tenant_id = tenant.id;
        this.edb_tenant_name = tenant.name;
        this.edb_status = status;
        this.edb_modal.show();
      },
      async submit_edb() {
        try {
          const response = await axios.post("/admin/update_tenant_edb", {id:this.edb_tenant_id, status:this.edb_status});
          if(response.data.status === "I")
          {
            //Force log out
            this.edb_modal_message = "Token expired. You will be logged out soon...";
            setTimeout(function() {window.location.href = "/admin/logout";}, 3000);
          }
          else
          {
            this.edb_modal.hide();
            this.get_tenants();
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
      async get_tenants() {
        $('#data-table').DataTable().destroy();
        this.tenants = [];
        const response = await axios.post("/admin/get_tenants", this.filter);
        this.tenants = response.data.data_list;
        Vue.nextTick(() => {
          $('#data-table').DataTable();
        });
      },
      change_filter() {
        this.get_tenants();
      }
    },
    delimiters: ["{","}"]
}).mount('#app');
</script>
@stop