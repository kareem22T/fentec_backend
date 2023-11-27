@extends('admin.layouts.admin-layout')

@section('title', 'Home')
@section('home_active', 'active')

@section('content')
<div class="home_wrapper" id="home_wrapper">
    <section class="row-1">
        <div class="statistics">
            <div class="card">
                <h1>
                    <span>+</span> <br>
                    Collected earning <br>
                    <span>2050</span>                    
                </h1>
            </div>
            <div class="card">
                <h1>
                    <span>-</span> <br>
                    Requested earning <br>
                    <span>1500</span>                    
                </h1>
            </div>
            <div class="card">
                <h1>
                    Total trips <br>
                    <span>9500</span>                    
                </h1>
            </div>
            <div class="card">
                <h1>
                    <span>+</span> <br>
                    Sold points <br>
                    <span>15000</span>                    
                </h1>
            </div>
            <button class="button">Quick access </button>
            <button class="button">Quick access </button>
        </div>
        <div class="card notification_wrapper">
            <input type="text" name="" id="title" class="input" placeholder="title">
            <textarea name="" id="msg" cols="30" rows="10" class="input" placeholder="Message"></textarea>
            <button class="button">Push Notification</button>
        </div>
    </section>
    <section class="row-2 table_wrapper">
        <h1>Coupon Codes</h1>
        <table>
            <thead>
                <tr>

                    <th>
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" placeholder="Title" class="input" v-model="title">
                    </th>
                    <th>
                        <label for="code">Code</label>
                        <input type="text" name="code" id="code" placeholder="Code" class="input" v-model="code">
                    </th>
                    <th>
                        <label for="start">Start in</label>
                        <input type="date" name="start" id="start" placeholder="Start in" class="input" v-model="start_in">
                    </th>
                    <th>
                        <label for="end">End in</label>
                        <input type="date" name="end" id="end" placeholder="End in" class="input" v-model="end_in">
                    </th>
                    <th>
                        <label for="">Controls</label>
                        <button class="button" @click="addCoupon()">Add</button>
                    </th>
                </tr>
            </thead>
            <tbody>
                @php
                    $coupons = App\Models\Coupon::all();
                @endphp
                @if ($coupons->count() > 0)
                    @foreach ($coupons as $coupon)
                    <tr>
                        <td>{{ $coupon->title }}</td>
                        <td>{{ $coupon->code }}</td>
                        <td>{{ $coupon->start_in }}</td>
                        <td>{{ $coupon->end_in }}</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </section>
</div>
@endsection

@section('scripts')
<script>
const { createApp, ref } = Vue;

createApp({
  data() {
    return {
        title: null,
        code: null,
        start_in: null,
        end_in: null
    }
  },
  methods: {
    async addCoupon() {
      $('.loader').fadeIn().css('display', 'flex')
        try {
            const response = await axios.post(`{{ route('coupon.put') }}`, {
                title: this.title,
                code: this.code,
                start_in: this.start_in,
                end_in: this.end_in,
            },
            {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }
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
  },
  created() {
    $('.loader').fadeOut()
  },
  mounted() {
  },
}).mount('#home_wrapper')
</script>
@endsection