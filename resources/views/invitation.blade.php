@extends('layout')
@section('title')
Dashboard
@stop
@section('header')
Invitations
@stop
@section('sub_header')
Invite visitors, update schedules and resend invitation emails
@stop
@section('style')
<link rel="stylesheet" href="{{ asset('css/daterange.css') }}" />
<link rel="stylesheet" href="{{ asset('css/full-calendar.min.css') }}" />
<link rel="stylesheet" href="{{ asset('css/full-calendar.custom.css') }}" />
<style>
.btn-group-xs > .btn, .btn-xs {
    padding: 1px 5px;
    font-size: 12px;
    line-height: 1.5;
}
	.bootstrap-tagsinput {
    width: 100%;
	min-height: 72px;
    padding: .375rem .75rem;
    font-weight: 400;
    color: #495057;
    display: flex;
	gap:8px;
    align-items: start;
    flex-wrap: wrap;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 4px;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}
.bootstrap-tagsinput .tag {
    line-height: 14px;
    font-size: .7rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.bootstrap-tagsinput .tag-close {
	margin-left:5px;
    cursor: pointer;
    width: 14px;
    height: 14px;
    line-height: 14px;
    font-size: 1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style>
@stop
@section('content')
<div id="app">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div>
                        <div ref="my_calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" ref="invite_modal" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
					    { modal_titles[m_active_ui] }
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
					<div v-show="m_active_ui === 1">
						<div class="row">
                			<div class="mb-3">
                  				<label class="form-label">Visitors * <button type="button" @click="show_add_visitor()" class="btn btn-xs btn-primary">
                         	 	<i class="icon-plus"></i>
                        		</button></label>
							<div class="bootstrap-tagsinput">
								<span v-for="c in invitation.visitors" class="badge border border-info bg-info-subtle text-info tag">{c.name} ({c.email}) <span class="tag-close" @click="remove_visitor(c.id)"><i class="icon-cancel"></i></span></span>
							</div>
							<div v-if="visitor_msg !== ''" class="ms-1 mt-1 text-danger">{visitor_msg}</div>
                		</div>
              			</div>
						<div class="row">
                			<div class="mb-3">
                  				<label class="form-label">Department</label>
								<input type="text" class="form-control" value="{{$dept_name}}" disabled>
                  				<!--select v-model="invitation.dept_id" class="form-select">
                      				<option value="0">Please select</option>
                      				<option v-for="d in depts" :value="d.dept_id">
                        				{d.full_name}
                      				</option>
                 				 </select-->
                			</div>
              			</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="mb-3">
									<label class="form-label" for="abc">Start Date</label>
                        			<input type="text" class="form-control" id="start_date">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="mb-3">
									<label class="form-label" for="abc">End Date</label>
                        			<input type="text" class="form-control" id="end_date">
									<div v-if="end_date_msg !== ''" class="ms-1 mt-1 text-danger">{end_date_msg}</div>
								</div>
							</div>
                    	</div>
						<div class="row">
							<div class="col">
								<label class="form-label">Interval for 1 day</label>
								<div class="row">
									<div class="col-sm-6">
										<div class="mb-3">
											<input type="text" class="form-control" id="interval">
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-check mb-3">
                        					<input class="form-check-input" type="checkbox" @change="check_change" v-model="all_day">
                        					<label class="form-check-label">All day</label>
                      					</div>
									</div>
								</div>
							</div>
                    	</div>
					</div>
					<div v-show="m_active_ui === 2">
						<div class="row">
                			<div class="mb-3">
                  				<label class="form-label">Visitor Email *</label>
                  				<input type="text" class="form-control" v-model.trim="contact.email">
								<div v-if="email_msg !== ''" class="ms-1 mt-1 text-danger">{email_msg}</div>
                			</div>
              			</div>
					</div>
					<div v-show="m_active_ui === 3">
						<div class="row">
                			<div class="mb-3">
                  				<label class="form-label">Visitor Email *</label>
                  				<input type="text" class="form-control" v-model.trim="contact.email" disabled>
                			</div>
              			</div>
						<div class="row">
                			<div class="mb-3">
                  				<label class="form-label">First Name *</label>
                  				<input type="text" class="form-control" v-model.trim="contact.first_name">
								<div v-if="first_name_msg !== ''" class="ms-1 mt-1 text-danger">{first_name_msg}</div>
                			</div>
              			</div>
						<div class="row">
                			<div class="mb-3">
                  				<label class="form-label">Last Name *</label>
                  				<input type="text" class="form-control" v-model.trim="contact.last_name">
								<div v-if="last_name_msg !== ''" class="ms-1 mt-1 text-danger">{last_name_msg}</div>
                			</div>
              			</div>
						<div class="row">
                			<div class="mb-3">
                  				<label class="form-label">ID Card *</label>
                  				<input type="text" class="form-control" v-model.trim="contact.id_card">
								  <div v-if="id_card_msg !== ''" class="ms-1 mt-1 text-danger">{id_card_msg}</div>
                			</div>
              			</div>
						<div class="row">
                			<div class="mb-3">
                  				<label class="form-label">Mobile Phone</label>
                  				<input type="text" class="form-control" v-model.trim="contact.phone">
                			</div>
              			</div>
					</div>
					<div v-show="invite_modal_message !== ''">
						<div class="alert alert-primary d-flex align-items-center">
                        	<i class="icon-alert-triangle fs-2 me-2 lh-1"></i>
                        	{ invite_modal_message }
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
@stop
@section('script')
<!-- For using Modal-->
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/moment.min.js') }}"></script>
<script src="{{ asset('js/daterange.js') }}"></script>
<script src="{{ asset('js/full-calendar.min.js') }}"></script>
<script>
//External function
function test() {
    alert("Yo");
}
const { createApp } = Vue;
createApp({
    data() {
      return {
        m_active_ui: 1,
		modal_titles: {
			1: "Invite Visitors",
			2: "Add a Visitor",
			3: "Save and Add Visitor"
		},
		modal_oks: {
			1: "Submit",
			2: "Search",
			3: "OK"
		},
		modal_cancels: {
			1: "Close",
			2: "Cancel",
			3: "Cancel"
		},
        calendar: null,
        invite_modal: null,
		invite_modal_message: "",
		depts:[],
		visitors:[],
		contact: {
			id: 0,
			email: "",
			first_name: "",
			last_name: "",
			phone: "",
			id_card: ""
		},
		contact0: null,
		all_day: false,
		visitor_msg: "",
		end_date_msg: "",
		email_msg: "",
		first_name_msg: "",
		last_name_msg: "",
		id_card_msg: "",
        invitation: {
			dept_id: {{$dept_id}},
			visitors: [],
			start_date: "",
			end_date: "",
			start_time: "",
			end_time:""
        },
      }
    },
    mounted() {
		//this.init_ui();
		$("#start_date").daterangepicker({
  			singleDatePicker: true,
  			startDate: moment().startOf("hour"),
  			endDate: moment().startOf("hour").add(32, "hour"),
  			locale: {
    			format: "DD/MM/YYYY",
  			},
		});

		$("#end_date").daterangepicker({
  			singleDatePicker: true,
  			startDate: moment().startOf("hour"),
  			endDate: moment().startOf("hour").add(32, "hour"),
  			locale: {
    			format: "DD/MM/YYYY",
  			},
		});

		$("#interval").val("00:00 - 23:55");
		$("#interval").daterangepicker({
    		timePicker: true,
    		timePicker24Hour: true,
    		timePickerIncrement: 5,
    		locale: {
      			format: "HH:mm",
    		},
  		})
  		.on("show.daterangepicker", function (ev, picker) {
    		picker.container.find(".calendar-table").hide();
  		});

		//Initialize Calendar
        var calendarEl = this.$refs.my_calendar;
        var calendar = new FullCalendar.Calendar(calendarEl, {
		headerToolbar: {
			left: "prev,next today",
			center: "title",
			right: "dayGridMonth,timeGridWeek,timeGridDay",
		},
		//initialDate: "2022-10-12",
		initialDate: Date.now(),
		navLinks: true, // can click day/week names to navigate views
		selectable: true,
		selectMirror: true,
		select: this.show_create,
		eventClick: function (arg) {
            console.log(arg.event);
			if (confirm("Are you sure you want to delete this event?")) {
				arg.event.remove();
			}
		},
		editable: true,
		dayMaxEvents: true, // allow "more" link when too many events
		events: [],
	});
    this.calendar = calendar;
    this.calendar.render();

	//Initialize modal
    this.invite_modal = new bootstrap.Modal(this.$refs.invite_modal, { keyboard: false });

	this.contact0 = { ...this.contact };
    },
    methods: {
		async init_ui() {
            try {
          	    await this.get_invitation_depts();
            }
            catch(error) {
                console.log(error);
            }
        
        },
		clear_invite_modal() {
			this.invitation.visitors.splice(0, this.invitation.visitors.length);
			
			this.all_day = false;
			$("#interval").val("00:00 - 23:55");
			$("#interval").prop('disabled', false);
			var picker = $("#interval").data('daterangepicker');
			picker.container.find('.hourselect').eq(0).val('0').trigger('change');
            picker.container.find('.minuteselect').eq(0).val('0').trigger('change');
            picker.container.find('.hourselect').eq(1).val('23').trigger('change');
            picker.container.find('.minuteselect').eq(1).val('55').trigger('change');
		},
        submit() {
			alert("yo");
        },
        show_create(arg) {
			this.m_active_ui = 1;
			this.visitor_msg = "";
			this.end_date_msg = "";

			this.clear_invite_modal();

			$("#start_date").data('daterangepicker').setStartDate(arg.start);
			$("#start_date").data('daterangepicker').setEndDate(arg.start);

			arg.end.setDate(arg.end.getDate() - 1);
			$("#end_date").data('daterangepicker').setStartDate(arg.end);
			$("#end_date").data('daterangepicker').setEndDate(arg.end);

			
            //this.active_ui = 2;
            /*this.calendar.removeAllEvents();
            this.calendar.addEvent(
                {
                    id: 10,
				    title: "Yo",
				    start: "2022-10-24",
				    color: "#dfffe1",
				    borderColor: "#1c8b24",
				    textColor: "#1c8b24",
			    },
            );
            console.log(this.calendar);
            */
            this.calendar.unselect();
            this.invite_modal.show();
        },
		async get_invitation_depts() {
          const response = await axios.post("/admin/get_invitation_depts");
          this.depts = response.data.data_list;
        },
		show_add_visitor() {
			MyApp.copy_vals(this.contact0, this.contact);
			this.m_active_ui = 2;
		},
		check_change() {
			if(this.all_day)
				$("#interval").prop('disabled', true);
			else
				$("#interval").prop('disabled', false);
		},
		remove_visitor(id) {
			for (let i = 0; i < this.invitation.visitors.length; i++) {
                if (this.invitation.visitors[i].id === id)
                    this.invitation.visitors.splice(i, 1);
            }
		},
		async modal_ok() {
			if(this.m_active_ui === 1)
			{
				let is_valid = true;
				this.visitor_msg = "";
				this.end_date_msg = "";
				if(this.invitation.visitors.length === 0)
				{
					this.visitor_msg = "Please add some visitors";
					is_valid = false;
				}

				this.invitation.start_date = $("#start_date").val();
				this.invitation.end_date = $("#end_date").val();

				let d1 = moment(this.invitation.start_date, "DD/MM/YYYY");
				let d2 = moment(this.invitation.end_date, "DD/MM/YYYY");

				if (d1 > d2) {
                	this.end_date_msg = "End Date is greater";
					is_valid = false;
            	}

				if(!is_valid)
					return;
			}
			else if(this.m_active_ui === 2)
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

				try{
					const response = await axios.post("/admin/get_contact", {email:this.contact.email});
					if(response.data.contact)
          				this.contact = response.data.contact;

					this.m_active_ui = 3;
				}
				catch(error){
					console.log(error);
					this.invite_modal_message = "Server error. Please try again later."
				}
			}
			else if(this.m_active_ui === 3)
			{
				let is_valid = true;
				if (this.contact.first_name === "")
				{
					this.first_name_msg = "First name is blank";
					is_valid = false;
				}
				if (this.contact.last_name === "")
				{
					this.last_name_msg = "Last name is blank";
					is_valid = false;
				}
				if (this.contact.id_card === "")
				{
					this.id_card_msg = "ID card is blank";
					is_valid = false;
				}

				if(!is_valid) return;
				
				let duplicated = false;
                for (let i = 0; i < this.invitation.visitors.length; i++)
                {
                    if (this.invitation.visitors[i].email === this.contact.email) 
					{
                        duplicated = true;
                        break;  
                	}
				}
				if (!duplicated)
					this.invitation.visitors.push({id: this.contact.id, email: this.contact.email, name: `${this.contact.first_name} ${this.contact.last_name}`});

				this.m_active_ui = 1;
			}
		},
		modal_cancel() {
			if(this.m_active_ui === 1)
				this.invite_modal.hide();
			else if(this.m_active_ui === 2)
				this.m_active_ui = 1;
			else if(this.m_active_ui === 3)
				this.m_active_ui = 1;
		}
    },
    delimiters: ["{","}"]
}).mount('#app');
</script>
@stop