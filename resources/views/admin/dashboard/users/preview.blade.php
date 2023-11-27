@extends('admin.layouts.admin-layout')

@section('title', 'Users')
@section('users_active', 'active')

@section('content')
<div class="users_wrapper" id="users_wrapper">
    <section class="row-2 table_wrapper">
        <div class="head">
            <h1>Users Requests</h1>
            <div class="pagination"></div>
            <div class="form-group search">
                <input type="text" name="search" id="search" placeholder="Search Users" class="input">
                <i class='bx bx-search'></i>
            </div>
        </div>
        <table class="normal_table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Controls</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="usersRequests && usersRequests.length > 0" v-for="user in usersRequests" :key="user.id">
                    <td>@{{user.name}}</td>
                    <td>@{{user.dob}}</td>
                    <td>@{{user.email}}</td>
                    <td>@{{user.phone}}</td>
                    <td>
                        <div class="btns flex-center">
                            <button class="button" @click="handleShowRequest(user)"><i class='bx bx-show-alt'></i></button>
                            {{-- <button class="button secondary" @click=""><i class='bx bx-envelope'></i></button> --}}
                            <button class="button success" @click="approveUser(user.id)"><i class='bx bx-check-circle' ></i></button>
                            <button class="button danger"  @click="selectedRequest = user;handleShowRejectionReasonPopUp()"><i class='bx bxs-user-x' ></i></button>
                        </div>
                    </td>
                </tr>
                <tr v-if="!usersRequests || usersRequests.length == 0" style="font-size: 20px; font-weight: 700; text-align: center">
                    <td colspan="5"><h2>There is no requests!</h2></td>
                </tr>
            </tbody>
        </table>
    </section>
    <section class="row-2 table_wrapper">
        <div class="head">
            <h1>Users List</h1>
            <div class="pagination"></div>
            <div class="form-group search">
                <input type="text" name="search" id="search" placeholder="Search Users" class="input">
                <i class='bx bx-search'></i>
            </div>
        </div>
        <table class="normal_table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Controls</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="usersList && usersList.length > 0" v-for="user in usersList" :key="user.id">
                    <td>@{{user.name}}</td>
                    <td>@{{user.dob}}</td>
                    <td>@{{user.email}}</td>
                    <td>@{{user.phone}}</td>
                    <td>
                        <div class="btns flex-center">
                            <button class="button" @click=""><i class='bx bx-show-alt'></i></button>
                            {{-- <button class="button secondary" @click=""><i class='bx bx-envelope'></i></button> --}}
                        </div>
                    </td>
                </tr>
                <tr v-if="!usersList || usersList.length == 0" style="font-size: 20px; font-weight: 700; text-align: center">
                    <td colspan="5"><h2>There is no Users!</h2></td>
                </tr>
            </tbody>
        </table>
    </section>
    <section class="row-2 table_wrapper" v-if="incompleteUsers && incompleteUsers.length > 0" >
        <div class="head">
            <h1>Users Incomplete Registeration</h1>
            <div class="pagination"></div>
            <div class="form-group search">
                <input type="text" name="search" id="search" placeholder="Search Users" class="input">
                <i class='bx bx-search'></i>
            </div>
        </div>
        <table class="normal_table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Phone</th>
                    {{-- <th>Controls</th> --}}
                </tr>
            </thead>
            <tbody>
                <tr v-if="incompleteUsers && incompleteUsers.length > 0" v-for="user in incompleteUsers" :key="user.id">
                    <td>@{{user.email}}</td>
                    <td>@{{user.phone}}</td>
                    {{-- <td>
                        <div class="btns flex-center">
                            <button class="button secondary" @click=""><i class='bx bx-envelope'></i></button>
                        </div>
                    </td> --}}
                </tr>
                <tr v-if="!incompleteUsers || incompleteUsers.length == 0" style="font-size: 20px; font-weight: 700; text-align: center">
                    <td colspan="5"><h2>There is no users!</h2></td>
                </tr>
            </tbody>
        </table>
    </section>

    <div class="hide-content" v-if="showRequestDetails || rejectionReasonPopUp || showRjectionAlert"></div>
    <div class="pop-up show_request_details_wrapper card" v-if="selectedRequest && showRequestDetails">
        <div class="head">
            <div class="profile">
                <i class='bx bxs-user'  v-if="!selectedRequest.photo_path"></i>
                <img v-if="selectedRequest.photo_path" :src="'/images/uploads/' + selectedRequest.photo_path">
            </div>
            <div class="identity">
                <img :src="'/images/uploads/' + selectedRequest.identity_path" alt="">
                <a :href="'/images/uploads/' + selectedRequest.identity_path" download="download" class="after">
                    <i class='bx bx-download' ></i>
                </a>
            </div>
        </div>
        <div class="details">
            <div>
                <i class='bx bxs-user' ></i> @{{selectedRequest.name}}
            </div>
            <div>
                <i class='bx bx-envelope' ></i> @{{selectedRequest.email}}
            </div>
            <div>
                <i class='bx bx-phone' ></i> @{{selectedRequest.phone}}
            </div>
        </div>
        <div class="btns flex-center">
            <button class="button secondary" @click="showRequestDetails = false">Cancel</button>
            <button class="button success" @click="approveUser(selectedRequest.id)">Approve</button>
            <button class="button danger" @click="handleShowRejectionReasonPopUp()">Reject</button>
        </div>
    </div>

    <div class="pop-up card rejection_reason_pop_up" v-if="rejectionReasonPopUp">
        <h2>What is the rejection reason</h2>
        <div class="btns flex-center choices">
            <button :class="rejection_choice == 1 ? 'active' : ''" @click="handleRejectionReason(1, 'Your Identity image is not avilable please Upload it')">Identity not avilable</button>
            <button :class="rejection_choice == 2 ? 'active' : ''" @click="handleRejectionReason(2, 'Your Identity image is not identical please Upload the correct one')">Identity not identical</button>
            <button :class="rejection_choice == 3 ? 'active' : ''" @click="handleRejectionReason(3, '')">Other</button>
        </div>
        <textarea name="reason" id="reason" cols="30" rows="10" class="input" placeholder="Type your reason" v-model="rejection_reason" v-if="rejection_choice == 3">
        </textarea>
        <div class="btns flex-center">
            <button class="button secondary" @click="rejectionReasonPopUp = false">Cancel</button>
            <button class="button danger" @click="handleRejectionAlert()">Reject</button>
        </div>
    </div>

    <div class="pop-up card rejection_reason_pop_up" v-if="showRjectionAlert">
        <h3>Are you sure your want to reject @{{ selectedRequest.name }} Account</h3>
        <div class="btns flex-center">
            <button class="button secondary" @click="showRjectionAlert = false">Cancel</button>
            <button class="button danger" @click="rejectUser(selectedRequest.id)">Reject</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const { createApp, ref } = Vue;

createApp({
  data() {
    return {
        usersRequests: null,
        usersList: null,
        incompleteUsers: null,
        selectedRequest: null,
        showRequestDetails: false,
        rejectionReasonPopUp: false,
        rejection_reason: "Your Identity image is not avilable please Upload it",
        rejection_choice: 1,
        showRjectionAlert: false,
    }
  },
  methods: {
    async getUsers() {
      $('.loader').fadeIn().css('display', 'flex')
        try {
            const response = await axios.post(`{{ route('get.users') }}`,
            {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }
            );
            if (response.data.status === true) {
                document.getElementById('errors').innerHTML = ''
                $('.loader').fadeOut()
                this.usersList = response.data.data.usersList
                this.usersRequests = response.data.data.usersRequests
                this.incompleteUsers = response.data.data.incompleteUsers
            } else {
                $('.loader').fadeOut()
                document.getElementById('errors').innerHTML = ''
                $.each(response.data.errors, function (key, value) {
                    let error = document.createElement('div')
                    error.classList = 'error'
                    error.innerHTML = value
                    document.getElementById('errors').append(error)
                });
                $('#errors').fadeIn('slow')
                setTimeout(() => {
                    $('input').css('outline', 'none')
                    $('#errors').fadeOut('slow')
                }, 5000);
            }

        } catch (error) {
            document.getElementById('errors').innerHTML = ''
            let err = document.createElement('div')
            err.classList = 'error'
            err.innerHTML = 'server error try again later'
            document.getElementById('errors').append(err)
            $('#errors').fadeIn('slow')
            $('.loader').fadeOut()

            setTimeout(() => {
            $('#errors').fadeOut('slow')
            }, 3500);

            console.error(error);
        }
    },
    async approveUser(id) {
      $('.loader').fadeIn().css('display', 'flex')
        try {
            const response = await axios.post(`{{ route('user.approve') }}`, {
                id: id,
            },
            );
            if (response.data.status === true) {
                document.getElementById('errors').innerHTML = ''
                let error = document.createElement('div')
                error.classList = 'success'
                error.innerHTML = response.data.message
                document.getElementById('errors').append(error)
                $('#errors').fadeIn('slow')
                $('.loader').fadeOut()
                setTimeout(() => {
                    $('#errors').fadeOut('slow')
                    window.location.reload()
                }, 2000);
            } else {
                $('.loader').fadeOut()
                document.getElementById('errors').innerHTML = ''
                $.each(response.data.errors, function (key, value) {
                    let error = document.createElement('div')
                    error.classList = 'error'
                    error.innerHTML = value
                    document.getElementById('errors').append(error)
                });
                $('#errors').fadeIn('slow')
                setTimeout(() => {
                    $('input').css('outline', 'none')
                    $('#errors').fadeOut('slow')
                }, 5000);
            }

        } catch (error) {
            document.getElementById('errors').innerHTML = ''
            let err = document.createElement('div')
            err.classList = 'error'
            err.innerHTML = 'server error try again later'
            document.getElementById('errors').append(err)
            $('#errors').fadeIn('slow')
            $('.loader').fadeOut()

            setTimeout(() => {
            $('#errors').fadeOut('slow')
            }, 3500);

            console.error(error);
        }
    },
    async rejectUser(id) {
      $('.loader').fadeIn().css('display', 'flex')
        try {
            const response = await axios.post(`{{ route('user.reject') }}`, {
                id: id,
                rejection_reason: this.rejection_reason
            },
            );
            if (response.data.status === true) {
                document.getElementById('errors').innerHTML = ''
                let error = document.createElement('div')
                error.classList = 'success'
                error.innerHTML = response.data.message
                document.getElementById('errors').append(error)
                $('#errors').fadeIn('slow')
                $('.loader').fadeOut()
                setTimeout(() => {
                    $('#errors').fadeOut('slow')
                    window.location.reload()
                }, 2000);
            } else {
                $('.loader').fadeOut()
                document.getElementById('errors').innerHTML = ''
                $.each(response.data.errors, function (key, value) {
                    let error = document.createElement('div')
                    error.classList = 'error'
                    error.innerHTML = value
                    document.getElementById('errors').append(error)
                });
                $('#errors').fadeIn('slow')
                setTimeout(() => {
                    $('input').css('outline', 'none')
                    $('#errors').fadeOut('slow')
                }, 5000);
            }

        } catch (error) {
            document.getElementById('errors').innerHTML = ''
            let err = document.createElement('div')
            err.classList = 'error'
            err.innerHTML = 'server error try again later'
            document.getElementById('errors').append(err)
            $('#errors').fadeIn('slow')
            $('.loader').fadeOut()

            setTimeout(() => {
            $('#errors').fadeOut('slow')
            }, 3500);

            console.error(error);
        }
    },
    handleShowRejectionReasonPopUp() {
        this.rejectionReasonPopUp = true;
        this.showRequestDetails = false
    },
    handleRejectionReason(choice, reason) {
        this.rejection_choice = choice
        this.rejection_reason = reason
    },
    handleRejectionAlert() {
        if (!this.rejection_reason) {
            $('.loader').fadeOut()
            document.getElementById('errors').innerHTML = ''
                let error = document.createElement('div')
                error.classList = 'error'
                error.innerHTML = "please enter rejection reason"
                document.getElementById('errors').append(error)
            $('#errors').fadeIn('slow')
            setTimeout(() => {
                $('input').css('outline', 'none')
                $('#errors').fadeOut('slow')
            }, 3000);
        } else {
            this.showRjectionAlert = true; 
            this.rejectionReasonPopUp = false
        }

    },
    handleShowRequest(user) {
        this.selectedRequest = user;
        this.showRequestDetails = true;
    }
  },
  created() {
    $('.loader').fadeOut()
    this.getUsers()
  },
  mounted() {
  },
}).mount('#users_wrapper')
</script>
@endsection