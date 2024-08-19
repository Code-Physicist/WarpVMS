<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="api-base-url" content="{{ url('') }}" />
  <title>Visitor Invitation</title>
  <link rel="shortcut icon" href="{{ asset('images/favicon.svg') }}" />
  <link rel="stylesheet" href="{{ asset('fonts/icomoon/style.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/main.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/dataTables.bs5.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/dataTables.bs5-custom.css') }}" />
</head>
<body style="background-color:#1a2752;">
  <div id="app">
    <div class="container">
      <div class="bg-white p-4 mt-4">
        <div class="row mb-2">
          <div class="col-12">
              <h3 class="mb-4">Update Information & Accept PDPA</h3>
              <div class="alert alert-info fade show" role="alert">
              - Please update your information and accept PDPA agreement.<br>
              - You will receive QR code email to access the building after accepting and submit<br>
              - You can also add new visitors and approve PDPA for their behalf
              </div>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col-12">
              <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center justify-content-start">
                  <button @click="show_create" class="btn btn-danger btn-sm me-2"><span class="icon-add"></span></button>
                  Add Visitor
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-striped" style="width:100%" id="data-table">
                  <thead>
                    <tr>
                      <th>
                        #
                      </th>
                      <th>
                        Visitor Name
                      </th>
                      <th>
                        Email Address
                      </th>
                      <th>
                        PDPA
                      </th>
                      <th>
                        <div>
                          Action
                        </div>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(visitor, index) in visitors">
                    <td>{index+1}.</td>
                    <td>{visitor.first_name} {visitor.last_name}</td>
                    <td>{visitor.email}</td>
                    <td>
                        <span v-if="visitor.pdpa_accept == 1" class="badge bg-success">Accepted</span>
                        <span v-else class="badge bg-secondary">Not Accepted</span>
                    </td>
                    <td>
                        <button class="btn btn-info btn-sm" @click="show_edit(visitor)">Accept</button>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
          </div>
        </div>     
      </div>
    </div>
    <div class="modal fade" ref="add_modal" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                      Add new visitor
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
					<div v-show="m_active_ui === 1">
						<div class="row">
                			<div class="mb-3">
                  				<label class="form-label">Visitor Email *</label>
                  				<input type="text" class="form-control" v-model.trim="contact.email">
								<div v-if="email_msg !== ''" class="ms-1 mt-1 text-danger">{email_msg}</div>
                			</div>
              			</div>
					</div>
					<div v-show="m_active_ui === 2">
						<div class="row">
              <div class="mb-3">
                <label class="form-label">Visitor Email *</label>
                <input type="text" class="form-control" v-model.trim="contact.email" disabled>
              </div>
             </div>
             <div class="row">
                    <div class="col-sm-6">
                      <div class="mb-3">
                  		  <label class="form-label">First Name *</label>
                  		  <input type="text" class="form-control" v-model.trim="contact.first_name">
								        <div v-if="first_name_msg !== ''" class="ms-1 mt-1 text-danger">{first_name_msg}</div>
                		  </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <label class="form-label">Last Name *</label>
                  		  <input type="text" class="form-control" v-model.trim="contact.last_name">
								        <div v-if="last_name_msg !== ''" class="ms-1 mt-1 text-danger">{last_name_msg}</div>
                		  </div>
                    </div>
              		</div>
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <label class="form-label">ID Card *</label>
                  		  <input type="text" class="form-control" v-model.trim="contact.id_card">
								        <div v-if="id_card_msg !== ''" class="ms-1 mt-1 text-danger">{id_card_msg}</div>
                		  </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <label class="form-label">Phone No. *</label>
                  		  <input type="text" class="form-control" v-model.trim="contact.phone">
								        <div v-if="phone_msg !== ''" class="ms-1 mt-1 text-danger">{phone_msg}</div>
                		  </div>
                    </div>
              		</div>
                  <div class="row">
							      <div class="col">
								      <div class="mb-3">
									      <label class="form-label">PDPA Consent</label>
                        <div class="border" style="height:200px;overflow-y: scroll;">
                          <p class="p-2">{pdpa_consent}</p>
                        </div>
								      </div>
							      </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col d-flex justify-content-end">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" :checked="contact.pdpa_accept == 1" @change="set_accept(1)">
                        <label class="form-check-label">Accept</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" :checked="contact.pdpa_accept == 0" @change="set_accept(0)">
                        <label class="form-check-label">Reject</label>
                      </div>
                    </div>
                  </div>
                  <div v-show="modal_message !== ''" class="mt-3">
						        <div class="alert alert-primary d-flex align-items-center">
                      <i class="icon-alert-triangle fs-2 me-2 lh-1"></i>
                      { modal_message }
                    </div>
					        </div>
					  </div>
          <div class="modal-footer">
                    <button @click="modal_cancel" type="button" class="btn btn-secondary">
						{ modal_cancels[m_active_ui] }
                    </button>
                    <button @click="modal_ok" type="button" class="btn btn-primary">
						{ modal_oks[m_active_ui] }
                    </button>
          </div>
				</div>
            </div>
        </div>
    </div>
	<div class="modal fade" ref="edit_modal" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
					            Update Information & Accept PDPA
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="row">
                		<div class="mb-3">
                  			<label class="form-label">Visitor Email </label>
                  			<input type="text" class="form-control" v-model.trim="contact.email" disabled>
                		</div>
              		</div>
						      <div class="row">
                    <div class="col-sm-6">
                      <div class="mb-3">
                  		  <label class="form-label">First Name *</label>
                  		  <input type="text" class="form-control" v-model.trim="contact.first_name">
								        <div v-if="first_name_msg !== ''" class="ms-1 mt-1 text-danger">{first_name_msg}</div>
                		  </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <label class="form-label">Last Name *</label>
                  		  <input type="text" class="form-control" v-model.trim="contact.last_name">
								        <div v-if="last_name_msg !== ''" class="ms-1 mt-1 text-danger">{last_name_msg}</div>
                		  </div>
                    </div>
              		</div>
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <label class="form-label">ID Card *</label>
                  		  <input type="text" class="form-control" v-model.trim="contact.id_card">
								        <div v-if="id_card_msg !== ''" class="ms-1 mt-1 text-danger">{id_card_msg}</div>
                		  </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <label class="form-label">Phone No. *</label>
                  		  <input type="text" class="form-control" v-model.trim="contact.phone">
								        <div v-if="phone_msg !== ''" class="ms-1 mt-1 text-danger">{phone_msg}</div>
                		  </div>
                    </div>
              		</div>
                  <div class="row">
							      <div class="col">
								      <div class="mb-3">
									      <label class="form-label">PDPA Consent</label>
                        <div class="border" style="height:200px;overflow-y: scroll;">
                          <p class="p-2">{pdpa_consent}</p>
                        </div>
								      </div>
							      </div>
                  </div>
                  <div class="row">
                    <div class="col d-flex justify-content-end">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" :checked="contact.pdpa_accept == 1" @change="set_accept(1)">
                        <label class="form-check-label">Accept</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" :checked="contact.pdpa_accept == 0" @change="set_accept(0)">
                        <label class="form-check-label">Reject</label>
                      </div>
                    </div>
                  </div>
                  <div v-show="modal_message !== ''" class="mt-3">
						        <div class="alert alert-primary d-flex align-items-center">
                      <i class="icon-alert-triangle fs-2 me-2 lh-1"></i>
                      { modal_message }
                    </div>
					        </div>
                </div>
                <div class="modal-footer pe-4 mt-0">
                    <button @click="edit_modal.hide()" type="button" class="btn btn-secondary">
						          Cancel
                    </button>
                    <button @click="submit_edit()" type="button" class="btn btn-primary">
                      Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
  </div>
</body>
</html>
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('js/validator.min.js') }}"></script>
    
<!-- Custom JS files -->
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/vue.global.prod.js') }}"></script>
<script>
const { createApp } = Vue;
createApp({
    data() {
      return {
        invite_id: {{$id}},
        invitation: null,
        v_dept_id: -1,
        visitors:[],
        dept: null,
        m_active_ui: 1,
        modal_oks: {
			    1: "Search",
			    2: "Submit"
		    },
		    modal_cancels: {
			    1: "Close",
			    2: "Cancel",
		    },
        contact: {
          id: 0,
			    email: "",
			    first_name: "",
			    last_name: "",
          id_card: "",
          phone: "",
          pdpa_accept: 0,
        },
        contact0: null,
        pdpa_consent: "",
        email_msg: "",
		    first_name_msg: "",
		    last_name_msg: "",
        id_card_msg: "",
        phone_msg: "",
        pdpa_msg: "",
        modal_message: "",
        add_modal: null,
		    edit_modal: null,
      }
    },
    mounted() {
      this.contact0 = {...this.contact};
      //Init modal
      this.add_modal = new bootstrap.Modal(this.$refs.add_modal, { keyboard: false });
	    this.edit_modal = new bootstrap.Modal(this.$refs.edit_modal, { keyboard: false });
      this.get_pdpa();
      this.get_invitation();
    },
    methods: {
      show_create() {
        this.clear_messages();
        this.m_active_ui = 1;
        MyApp.copy_vals(this.contact0, this.contact);
        this.add_modal.show();
      },
      show_edit(visitor) {
        this.clear_messages();
        MyApp.copy_vals(visitor, this.contact);
        this.contact.pdpa_accept = 0;
        this.edit_modal.show();
      },
      clear_messages() {
        this.email_msg = "";
		    this.first_name_msg = "";
		    this.last_name_msg = "";
        this.id_card_msg = "";
        this.phone_msg = "";
        this.pdpa_msg = "";
        this.modal_message = "";
      },
      async submit_edit() {
        this.clear_messages();

        let is_valid = true;
				if (this.contact.first_name === "")
				{
					this.first_name_msg = "First Name is blank";
					is_valid = false;
				}
				if (this.contact.last_name === "")
				{
					this.last_name_msg = "Last Name is blank";
					is_valid = false;
				}
        if (this.contact.id_card === "")
				{
					this.id_card_msg = "ID Card is blank";
					is_valid = false;
				}
        if (this.contact.phone === "")
				{
					this.phone_msg = "Phone No. is blank";
					is_valid = false;
				}
        if (this.contact.pdpa_accept == 0)
				{
					this.modal_message = "Please accept PDPA consent to continue";
					is_valid = false;
				}

				if(!is_valid) return;

        try {
          const response = await axios.post("/visitors/edit_visitor", {invite_id: this.invite_id, contact: this.contact});
          if(response.data.status === "T") {
            if(response.data.send_email) {
              axios.post("/admin/send_qr_code", {invite_id: this.invite_id, email: this.contact.email});
            }
            this.get_invitation();
            this.edit_modal.hide();
          }
        }
        catch(error)
        {
          console.log(error);
        }
      },
      async get_invitation() {
        try {
          $('#data-table').DataTable().destroy();
          this.visitors = [];
          const response = await axios.post("/visitors/get_invitation", {id: this.invite_id});
          this.v_dept_id = response.data.v_dept_id;
          this.visitors = response.data.visitors;
          this.invitation = response.data.invitation;
          this.dept = response.data.dept;
          Vue.nextTick(() => {
            $('#data-table').DataTable({
              lengthChange: false,
              searching: false,
              pageLength: 10,
            });
          });
        }
        catch(error) {
          console.log(error);
        }
      },
      async get_pdpa() {
        try{
          const response = await axios.post("/visitors/get_pdpa_consent");
          this.pdpa_consent = response.data.pdpa_consent;
        }
        catch(error){
          console.log(error);
        }
      },
      set_accept(value) {
        if (this.contact.pdpa_accept == value) {
          this.contact.pdpa_accept = value == 1 ? 0 : 1;  // Switch to the other value
        } else {
          this.contact.pdpa_accept = value;
        }
      },
      async modal_ok() {
			if(this.m_active_ui === 1)
			{
				this.email_msg = "";
				if (this.contact.email === "")
				{
					this.email_msg = "Visitor Email is blank";
					return;
				}
				if (!validator.isEmail(this.contact.email))
				{
					this.email_msg = "Incorrect Email format";
					return;
				}
        for(let i = 0; i < this.visitors.length; i++)
        {
          if(this.visitors[i].email === this.contact.email)
          {
            this.email_msg = "This person is already in the invitation";
					  return;
          }
        }

				try{
					const response = await axios.post("/visitors/get_contact", {v_dept_id: this.v_dept_id, email:this.contact.email});
					let contact = response.data.contact;
          if(contact !== null)
          {
            contact.pdpa_accept = 0;
            this.contact = contact;
          }
          else {
            this.contact.first_name = "";
            this.contact.last_name = "";
            this.contact.id_card = "";
            this.contact.phone = "";
            this.contact.pdpa_accept = 0;
          }
					this.m_active_ui = 2;
				}
				catch(error){
					console.log(error);
					this.modal_message = "Server error. Please try again later."
				}
			}
			else if(this.m_active_ui === 2)
			{
        this.clear_messages();
				let is_valid = true;
				if (this.contact.first_name === "")
				{
					this.first_name_msg = "First Name is blank";
					is_valid = false;
				}
				if (this.contact.last_name === "")
				{
					this.last_name_msg = "Last Name is blank";
					is_valid = false;
				}
        if (this.contact.id_card === null || this.contact.id_card === "")
				{
					this.id_card_msg = "ID Card is blank";
					is_valid = false;
				}
        if (this.contact.phone === null || this.contact.phone === "")
				{
					this.phone_msg = "Phone No. is blank";
					is_valid = false;
				}
        if (this.contact.pdpa_accept == 0)
				{
					this.modal_message = "Please accept PDPA consent to continue";
					is_valid = false;
				}

				if(!is_valid) return;
        
				try{
					const response = await axios.post("/visitors/add_visitor", {invite_id: this.invite_id, v_dept_id: this.v_dept_id, contact:this.contact});
					console.log(response);
          if(response.data.status === "T")
          {
            axios.post("/admin/send_qr_code", {invite_id: this.invite_id, email: this.contact.email});
            this.get_invitation();
            this.add_modal.hide();
					}
          if(response.data.status === "F")
          {
            console.log(response.data.err_message);
					}
				}
				catch(error){
					console.log(error);
					this.modal_message = "Server error. Please try again later."
				}
			}
		},
		modal_cancel() {
			if(this.m_active_ui === 1)
				this.add_modal.hide();
			else if(this.m_active_ui === 2)
				this.m_active_ui = 1;
		}
    },
    delimiters: ["{","}"]
}).mount('#app');
</script>