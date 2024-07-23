@extends('layout')
@section('title')
Invitations
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
								<!--input type="text" class="form-control" value="{{$dept_name}}" disabled-->
                  				<select v-model="invitation.to_dept_id" class="form-select">
                      				<option value="-1">Please select</option>
                      				<option v-for="d in depts" :value="d.dept_id">
                        				{d.full_name}
                      				</option>
                 				 </select>
								  <div v-if="to_dept_msg !== ''" class="ms-1 mt-1 text-danger">{to_dept_msg}</div>
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
	<div class="modal fade" ref="event_modal" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
					    {event.title}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
					<div class="row mb-2">
						<div class="col-5 text-end">
							<strong>Dates:</strong>
						</div>
						<div class="col-7 text-start">
							{event.start_date} - {event.end_date}
						</div>
					</div>
					<div class="row mb-4">
						<div class="col-5 text-end">
							<strong>Time Interval:</strong>
						</div>
						<div class="col-7 text-start">
							{event.interval}
						</div>
					</div>
					<div class="table-outer mx-3 mb-3">
						<div>
						<table class="table table-bordered m-0">
						<thead>
							<tr>
								<th>#</th>
								<th>Visitor Name</th>
								<th>Email Address</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(visitor, index) in event.visitors">
                    			<td>{index+1}.</td>
								<td>{visitor.first_name} {visitor.last_name}</td>
                    			<td>{visitor.email}</td>
							</tr>
						</tbody>
					</table>
						</div>
					</div>
					
				</div>
                <div class="modal-footer pe-4">
                    <button v-show="admin_level_id > 3" @click="event_modal.hide()" type="button" class="btn btn-secondary">
						Cancel
                    </button>
                    <button @click="resend()" type="button" class="btn btn-primary">
						{ admin_level_id > 3? 'Resend Email':'Close'}
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
const { createApp } = Vue;
createApp({
    data() {
      return {
		admin_level_id: {{$admin_level_id}},
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
		p_start_time: "00:00",
		p_end_time: "23:55",
        calendar: null,
		cal_start_date: null,
		cal_end_date: null,
		cal_view_type: null,
		colors: [
            {color: "#e8f4ff", borderColor: "#0b5aa9", textColor: "#0b5aa9"},
            {color: "#e8f4d6", borderColor: "#53810c", textColor: "#53810c"},
            {color: "#ffebeb", borderColor: "#ce385b", textColor: "#ce385b"},
            {color: "#fff5d5", borderColor: "#d49d1c", textColor: "#d49d1c"},
            {color: "#e2ddff", borderColor: "#7164b5", textColor: "#7164b5"},
            {color: "#ffe6fe", borderColor: "#780974", textColor: "#780974"},
            {color: "#ffeade", borderColor: "#d45c1c", textColor: "#d45c1c"},
        ],
        invite_modal: null,
		event_modal: null,
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
		to_dept_msg: "",
		end_date_msg: "",
		email_msg: "",
		first_name_msg: "",
		last_name_msg: "",
		id_card_msg: "",
        invitation: {
			to_dept_id: -1,
			visitors: [],
			start_date: "",
			end_date: "",
			start_time: "",
			end_time:""
        },
		event: {
			invite_id: 0,
			title: "",
			start_date: "",
			end_date: "",
			interval: "",
			visitors: []
		},
      }
    },
    mounted() {
		this.contact0 = { ...this.contact };
		this.init_ui();
		
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
		datesSet: this.dates_set,
		eventClick: this.show_event,
		editable: false,
		dayMaxEvents: true, // allow "more" link when too many events
		slotLabelFormat: {
          hour: '2-digit',
          minute: '2-digit',
          hour12: false
        },
        eventTimeFormat: {
          hour: '2-digit',
          minute: '2-digit',
          hour12: false
        },
		events: [],
		});
    	this.calendar = calendar;
    	this.calendar.render();

	//Initialize modal
    this.invite_modal = new bootstrap.Modal(this.$refs.invite_modal, { keyboard: false });
	this.event_modal = new bootstrap.Modal(this.$refs.event_modal, { keyboard: false });
    },
    methods: {
		async init_ui() {
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
  			.on("show.daterangepicker", this.show_picker);

            try {
          	    await this.get_invitation_depts();
            }
            catch(error) {
                console.log(error);
            }
        },
		show_picker(ev, picker) {
			picker.container.find(".calendar-table").hide();

			let start_time_array = this.p_start_time.split(':');
			let end_time_array = this.p_end_time.split(':');
			picker.container.find('.hourselect').eq(0).val(parseInt(start_time_array[0]), 0).trigger('change');
            picker.container.find('.minuteselect').eq(0).val(parseInt(start_time_array[1]), 0).trigger('change');
            picker.container.find('.hourselect').eq(1).val(parseInt(end_time_array[0]), 23).trigger('change');
            picker.container.find('.minuteselect').eq(1).val(parseInt(end_time_array[1]), 55).trigger('change');
		},
		clear_invite_modal() {
			this.invitation.visitors.splice(0, this.invitation.visitors.length);
		},
		dates_set(info) {
      		// Get the start and end dates of the current view
      		this.cal_start_date = info.start;
      		this.cal_end_date = info.end;
			this.cal_view_type = info.view.type;
			this.get_invitations();
		},
		async get_invitations() {
			try{
				this.calendar.removeAllEvents();
				const response = await axios.post("/admin/get_invitations", {
                    start_date: this.cal_start_date,
                    end_date: this.cal_end_date,
                });
          		let invitations = response.data.data_list;
				let c_index = 0;
				for(let i = 0; i < invitations.length; i++)
				{
					let inv = invitations[i];
					let start_date = this.get_date_obj(invitations[i].start_date);
					let end_date = this.get_date_obj(invitations[i].end_date);

					let start_time = `${this.format_time_str(inv.start_time)}`;
					let end_time = `${this.format_time_str(inv.end_time)}`;
					if(start_time === "00:00:00" && end_time === "23:55:00")
					{
						//Need to add one day for end date to display correctly
						let end_date2 = this.get_date_obj(invitations[i].end_date);
						end_date2.setDate(end_date2.getDate() + 1);

						this.calendar.addEvent(
                			{
                    			invite_id: inv.id,
				    			title: inv.dept_name,
				    			start: `${invitations[i].start_date}`,
								end: `${this.get_date_str(end_date2)}`,
				    			color: this.colors[c_index].color,
				    			borderColor: this.colors[c_index].borderColor,
				    			textColor: this.colors[c_index].textColor,

								start_date: `${invitations[i].start_date}`,
								end_date: `${invitations[i].end_date}`,
								start_time: `${start_time}`,
								end_time: `${end_time}`,
								all_day: true
			    			}
            			);

						if (c_index < this.colors.length - 1) c_index++;
                   		else c_index = 0;
						continue;
					}

					for (let d = start_date; d <= end_date; d.setDate(d.getDate() + 1)) {

						this.calendar.addEvent(
                			{
                    			invite_id: inv.id,
				    			title: inv.dept_name,
				    			start: `${this.get_date_str(d)}T${start_time}`,
								end: `${this.get_date_str(d)}T${end_time}`,
				    			color: this.colors[c_index].color,
				    			borderColor: this.colors[c_index].borderColor,
				    			textColor: this.colors[c_index].textColor,

								start_date: `${invitations[i].start_date}`,
								end_date: `${invitations[i].end_date}`,
								start_time: `${start_time.slice(0, -3)}`,
								end_time: `${end_time.slice(0, -3)}`,
								all_day: false,
			    			}
            			);
					}
					if (c_index < this.colors.length - 1) c_index++;
                    else c_index = 0;
				}
			}
			catch(error) {
				console.log(error);
			}
		},
		get_date_obj(date_str)
		{
			//Date format is "YYYY-MM-DD"
			const [year, month, day] = date_str.split('-').map(Number);
			return new Date(year, month - 1, day);
		},
		get_date_str(date_obj)
		{
			const year = date_obj.getFullYear();
			const month = String(date_obj.getMonth() + 1).padStart(2, '0'); // Months are zero-based
			const day = String(date_obj.getDate()).padStart(2, '0');
			return `${year}-${month}-${day}`;
		},
		format_date_str(date_str)
		{
			//Convert YYYY-MM-DD to "DD/MM/YYYY"
			const [year, month, day] = date_str.split('-');
			return `${day}/${month}/${year}`;
		},
		format_time_str(time_str)
		{
			time_array = time_str.split('.');
			return time_array[0];
		},
        show_create(arg) {
			if(this.admin_level_id == "2" || this.admin_level_id == "3")
			{	
				this.calendar.unselect();
				return;
			}

			this.m_active_ui = 1;
			this.to_dept_msg = "";
			this.visitor_msg = "";
			this.end_date_msg = "";

			this.clear_invite_modal();

			if(this.cal_view_type === "dayGridMonth")
			{
				$("#start_date").data('daterangepicker').setStartDate(arg.start);
				$("#start_date").data('daterangepicker').setEndDate(arg.start);

				arg.end.setDate(arg.end.getDate() - 1);
				$("#end_date").data('daterangepicker').setStartDate(arg.end);
				$("#end_date").data('daterangepicker').setEndDate(arg.end);

				this.all_day = false;
				this.p_start_time = "00:00";
				this.p_end_time = "23:55";
				$("#interval").val(`${this.p_start_time} - ${this.p_end_time}`);
				$("#interval").prop('disabled', false);
			}
			else{

				if (arg.start.getDate() !== arg.end.getDate()) {
					alert("Horizontal selection is not allowed in week mode");
					this.calendar.unselect();
					return;
				}

				let start_dt_array = arg.startStr.split('T');
				let end_dt_array = arg.endStr.split('T');
				$("#start_date").data('daterangepicker').setStartDate(new Date(start_dt_array[0]));
				$("#start_date").data('daterangepicker').setEndDate(new Date(start_dt_array[0]));

				$("#end_date").data('daterangepicker').setStartDate(new Date(start_dt_array[0]));
				$("#end_date").data('daterangepicker').setEndDate(new Date(start_dt_array[0]));

				if(arg.allDay)
				{
					this.all_day = true;
					this.p_start_time = "00:00";
					this.p_end_time = "23:55";
					$("#interval").val(`${this.p_start_time} - ${this.p_end_time}`);
					$("#interval").prop('disabled', true);
				}
				else {
					this.all_day = false;
					let start_time_array = start_dt_array[1].split(':');
					let end_time_array = end_dt_array[1].split(':');
					this.p_start_time = `${start_time_array[0]}:${start_time_array[1]}`;
					this.p_end_time = `${end_time_array[0]}:${end_time_array[1]}`;
					$("#interval").prop('disabled', false);
					$("#interval").val(`${this.p_start_time} - ${this.p_end_time}`);
				}
			}

            this.calendar.unselect();
            this.invite_modal.show();
        },
		async show_event(arg) {
            this.event.invite_id = arg.event.extendedProps.invite_id;
			this.event.title = arg.event.title;
			this.event.start_date = this.format_date_str(arg.event.extendedProps.start_date);
			this.event.end_date = this.format_date_str(arg.event.extendedProps.end_date);
			if(arg.event.extendedProps.all_day)
			{
				this.event.interval = "All day";
			}
			else {
				this.event.interval = `${arg.event.extendedProps.start_time} - ${arg.event.extendedProps.end_time}`;
			}

			try {
				const response = await axios.post("/admin/get_contacts", {invite_id: this.event.invite_id});
				this.event.visitors = response.data.data_list;
				this.event_modal.show();
			}
			catch(error) {
				console.log(error);
			}
		},
		resend() {
			if(this.admin_level_id <= 3) {
				this.event_modal.hide();
				return;
			}

			alert("Under construction");
			return;

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
				this.invite_modal_message = "";
				this.to_dept_msg = "";
				this.visitor_msg = "";
				this.end_date_msg = "";
				if(this.invitation.visitors.length === 0)
				{
					this.visitor_msg = "Please add some visitors";
					is_valid = false;
				}

				if(this.invitation.to_dept_id == "-1")
				{
					this.to_dept_msg = "Please select Department";
					is_valid = false;
				}

				let d1 = moment( $("#start_date").val(), "DD/MM/YYYY");
				let d2 = moment($("#end_date").val(), "DD/MM/YYYY");

				if (d1 > d2) {
                	this.end_date_msg = "End Date is greater";
					is_valid = false;
            	}

				if(!is_valid)
					return;

				this.invitation.start_date = d1.format("YYYY-MM-DD");
				this.invitation.end_date = d2.format("YYYY-MM-DD");

				if(this.all_day)
				{
					this.invitation.start_time = "00:00";
					this.invitation.end_time = "23:55";
				}
				else {
					let time_array = $("#interval").val().split(" ");
					this.invitation.start_time = time_array[0];
					this.invitation.end_time = time_array[2];
				}

				try{
					const response = await axios.post("/admin/create_invitation", this.invitation);
          			if(response.data.status === "I")
					{
						//Force log out
						this.invite_modal_message = "Token expired. You will be logged out soon...";
            			setTimeout(function() {window.location.href = "{{url('/admin/logout')}}";}, 3000);
						return;
					}
					else if(response.data.status === "F")
					{
						this.invite_modal_message = "Failed to create the inviation";
						console.log(response.data.err_message);
						return;
					}

					//No waiting until email sent
					axios.post("/admin/send_invite_email", {invite_id: response.data.invite_id, visitors: response.data.visitors});

					//refresh calendar
					this.get_invitations();
					this.invite_modal.hide();
				}
				catch(error) {
					console.log(error);
					this.invite_modal_message = "Failed to create the inviation";
				}
				
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
					this.id_card_msg = "ID Name is blank";
					is_valid = false;
				}

				if(!is_valid) return;

				try{
					const response = await axios.post("/admin/upsert_contact", this.contact);
					if(response.data.status === "I")
          			{
						//Force log out
						this.invite_modal_message = "Token expired. You will be logged out soon...";
            			setTimeout(function() {window.location.href = "{{url('/admin/logout')}}";}, 3000);
					}

					this.contact.id = response.data.contact_id;

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
				catch(error){
					console.log(error);
					this.invite_modal_message = "Server error. Please try again later."
				}
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