@extends('layout')
@section('title')
Update PDPA
@stop
@section('header')
PDPA Revisions
@stop
@section('sub_header')
Create, Update or Activate PDPA
@stop
@section('style')
<link rel="stylesheet" href="{{ asset('css/dataTables.bs5.css') }}" />
<link rel="stylesheet" href="{{ asset('css/dataTables.bs5-custom.css') }}" />
@stop
@section('content')
  <div v-show="active_ui === 1" class="row" style="display:none;">
    <div class="col-12">
      <div class="card mb-4">
      <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center justify-content-start mb-2">
              <button @click="show_create" class="btn btn-danger btn-sm mx-2"><span class="icon-add"></span></button>
              PDPA
            </div>
          </div>
          <div class="table-responsive">
              <table class="table table-striped" style="width:100%" id="data-table">
                <thead>
                  <tr>
                    <th>
                        Revision
                    </th>
                    <th>
                        Consent
                    </th>
                    <th>
                        Status
                    </th>
                    <th>
                        Actions
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
          <h5 class="card-title">Create PDPA</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="offset-md-3 col-md-6">
              <div class="row">
                <div class="mb-3">
                  <label class="form-label">Consent</label>
                  <textarea type="text" class="form-control" v-model.trim="pdpa.consent" rows="8" cols="50"></textarea>
                  <div v-if="consent_msg !== ''" class="ms-1 mt-1 text-danger">{consent_msg}</div>
                </div>
              </div>
              <div v-show="form_message !== ''">
						    <div class="alert alert-primary d-flex align-items-center">
                    <i class="icon-alert-triangle fs-2 me-2 lh-1"></i>
                    { form_message }
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
  <div class="modal rounded-3 fade" role="dialog" data-bs-backdrop="static" ref="atv_modal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-body px-4 pt-4 pb-4 text-center">
            <h5 class="text-primary">Activate this PDPA?</h5>
              <div class="mt-4 mb-2">
                  PDPA Ref. {pdpa_id}
              </div>
              <div v-show="modal_message !== ''" class="mb-0">
						    <div class="alert alert-primary d-flex justify-content-center align-items-center mb-0 p-2">
                    <i class="icon-alert-triangle fs-2 me-2 lh-1"></i>
                    { modal_message }
                </div>
					    </div>
          </div>
          <div class="modal-footer flex-nowrap p-0">
            <button type="button" @click="submit_atv" class="btn text-primary fs-6 col-6 m-0 border-end">
              <strong>Activate</strong>
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

function show_activate(id) {
  app.show_activate(id);
}
window.app = Vue.createApp({
    data() {
      return {
        active_ui: 1,
        pdpa_table: null,
        pdpas:[],
        pdpa: {
          id: 0,
          consent: "",
          create_time: "",
          is_active: 0,
        },
        pdpa0: null,
        consent_msg: "",
        form_message: "",
        atv_modal: null,
        pdpa_id: 0,
        modal_message: "",
      }
    },
    mounted() {
      this.pdpa0 = { ...this.pdpa };
      this.atv_modal = new bootstrap.Modal(this.$refs.atv_modal, { keyboard: false });
      this.init_data_table();
    },
    methods: {
      init_data_table() {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        this.pdpa_table = $("#data-table").DataTable({
          lengthChange: false,
          searching: false,
          pageLength: 5,
          processing: true,
          serverSide: true,
          ajax: {
            url: axios.defaults.baseURL + "/admin/get_pdpas",
            type: "POST",
            "headers": {
              "X-CSRF-TOKEN": csrfToken
            },
            dataSrc: this.post_process,
            error: function(xhr, error, thrown) {
              console.log(xhr);
              // Handle errors here
              console.log('AJAX Error: ', error); // Log error to console
            
              // Optional: Handle specific errors
              if (xhr.status === 404) {
                alert('Server not found (404).');
              } else if (xhr.status === 500) {
                alert('Server error (500).');
              }
              // You can add more specific error handling here
            }
          },
          columns: [
            { data: "id", orderable: true},
            { data: "consent_str", orderable: false},
            { data: "is_active", orderable: true},
            { data: "action", orderable: false},
          ],
          order: [
            [0, 'desc'],
          ]
        });
      },
      show_create() {
        MyApp.copy_vals(this.pdpa0, this.pdpa);
        this.active_ui = 2;
      },
      show_edit(id) {
        let pdpa = null;
        for(let i = 0; i < this.pdpas.length; i++)
        {
          if(this.pdpas[i].id == id)
          {
            pdpa = this.pdpas[i];
            break;
          }
        }

        this.consent_msg = "";
        this.form_message = "";

        MyApp.copy_vals(pdpa, this.pdpa);
        this.active_ui = 2;
      },
      show_activate(id) {
        this.pdpa_id = id;
        this.atv_modal.show();
      },
      async submit() {
        this.consent_msg = "";

        if (this.pdpa.consent === "") {
          this.consent_msg = "Please fill in the consent";
          return;
        }

        let url = (this.pdpa.id == 0) ? "/admin/create_pdpa" : "/admin/update_pdpa";
        try {
          const response = await axios.post(url, this.pdpa);
          if(response.data.status === "T")
          {
            //Make sure not to change datatable configurations
            this.pdpa_table.ajax.reload(null, false);
            this.active_ui = 1;
          }
          else if(response.data.status === "I")
          {
            //Force log out
            this.form_message = "Token expired. You will be logged out soon...";
            setTimeout(function() {window.location.href = "{{url('/admin/logout')}}";}, 3000);
          }
          else
          {
            this.form_message = "Invalid Data Submission";
          }
        }
        catch(error) {
          console.log(error);
          this.form_message = "Server error. Please try again later";
        }

      },
      async submit_atv() {
        try {
          const response = await axios.post("/admin/activate_pdpa", {id:this.pdpa_id});
          if(response.data.status === "I")
          {
            //Force log out
            this.modal_message = "Token expired. You will be logged out soon...";
            setTimeout(function() {window.location.href = "{{url('/admin/logout')}}";}, 3000);
          }
          else
          {
            this.atv_modal.hide();
            //Make sure not to change datatable configurations
            this.pdpa_table.ajax.reload(null, false);
            this.active_ui = 1;
          }
        }
        catch(error) {
          console.log(error);
          this.modal_message = "Server error. Please try again later.";
        }
      },
      post_process(json) {
        // Modify the json data here before DataTable uses it
        for (var i = 0; i < json.aaData.length; i++) {
          // Example: add a new field or modify an existing one
          //json.data[i].new_field = json.data[i].existing_field + " - modified";
          this.pdpas = json.aaData;
          let pdpa = this.pdpas[i];
          pdpa.consent_str = `<div class="crop">${pdpa.consent}</div>`;
          let is_active = pdpa.is_active;
          if(pdpa.is_active == 1) pdpa.is_active = "<span class='badge bg-success'>Active</span>";
          else pdpa.is_active = "<span class='badge bg-secondary'>Disabled</span>";
          
          pdpa.action = "<div class='d-flex justify-content-center align-items-center'>";
          pdpa.action += "<a class='cursor-pointer' data-bs-toggle='dropdown'><h5 class='mb-0'><i class='icon-more-vertical'></i></h5></a>";
          pdpa.action += "<ul class='dropdown-menu shadow-sm dropdown-menu-mini'>";
          pdpa.action += `<li><a class='dropdown-item cursor-pointer' onclick='show_edit(${pdpa.id})'><span class='icon-edit fs-5'></span> Edit</a></li>`;
          if(is_active == 0)
            pdpa.action += `<a class="dropdown-item cursor-pointer" onclick="show_activate(${pdpa.id}, 1)"><span class="icon-check-square fs-5"></span> Activate</a></li>`;
          pdpa.action += "</ul>";
          pdpa.action += "</div>";
        }
        return json.aaData;
      },
    },
    delimiters: ["{","}"]
}).mount('#app');
</script>
@stop