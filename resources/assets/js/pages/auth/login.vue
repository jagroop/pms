<template>

  <el-container>
    <el-card  class="box-card">
      <span style="margin: 0px auto;">{{$t('login')}}</span>
      <el-form ref="form" @submit.prevent="login" @keydown="form.onKeydown($event)" >     
        <el-form-item label=""> {{ $t('email') }}
          <el-input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" type="email" name="email"></el-input>
          <has-error :form="form" field="email"/>
        </el-form-item>
        <el-form-item label=""> {{ $t('password') }}
          <el-input placeholder="Please input password" v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" show-password type="password" name="password"></el-input>
          <has-error :form="form" field="password"/>
        </el-form-item>
        <el-form-item>
          <el-checkbox v-model="remember" name="remember">{{ $t('remember_me') }}</el-checkbox>
        </el-form-item>
        <el-form-item>
          <router-link :to="{ name: 'password.request' }" class="small ml-auto my-auto">
            {{ $t('forgot_password') }}
          </router-link>
        </el-form-item>
        <el-form-item>
          <v-button :loading="form.busy">
            {{ $t('login') }}
          </v-button>
        </el-form-item>
        <login-with-github/>
      </el-form>
    </el-card> 
  </el-container>
  <!-- <div class="row">
   

    <div class="col-lg-8 m-auto"> -->
      
      <!--   <div slot="header" class="clearfix"> -->
          
      <!--   </div> -->

<!-- <card :title="$t('login')"> -->

        

          <!-- Email -->
          <!-- <div class="form-group row"> -->
            
            <!-- <label class="col-md-3 col-form-label text-md-right">{{ $t('email') }}</label> -->
            <!-- <div class="col-md-7">
              <input v-model="form.email" :class="{ 'is-invalid': form.errors.has('email') }" class="form-control" type="email" name="email">
              <has-error :form="form" field="email"/>
            </div>
          </div> -->

          <!-- Password -->
           

          <!-- <div class="form-group row">
            <label class="col-md-3 col-form-label text-md-right">{{ $t('password') }}</label>
            <div class="col-md-7">
              <input v-model="form.password" :class="{ 'is-invalid': form.errors.has('password') }" class="form-control" type="password" name="password">
              <has-error :form="form" field="password"/>
            </div>
          </div> -->

          <!-- Remember Me -->
          <!-- <div class="form-group row"> -->
            <!-- <div class="col-md-3"/>
            <div class="col-md-7 d-flex"> -->
             
              <!-- <checkbox v-model="remember" name="remember">
                {{ $t('remember_me') }}
              </checkbox> -->
              
           <!--  </div>
          </div> -->
<!-- 
          <div class="form-group row">
            <div class="col-md-7 offset-md-3 d-flex"> -->
              <!-- Submit Button -->
              
              
              <!-- GitHub Login Button -->
              <!-- <login-with-github/> -->
          <!--   </div>
          </div> -->
          
     <!--  </card> -->
   <!--  </div>
  </div> -->
</template>

<style type="text/css">
  
.box-card {
    margin: 0px auto;
    width: 40.8%;
    
  }

</style>
<script>
import Form from 'vform'
import LoginWithGithub from '~/components/LoginWithGithub'

export default {
  middleware: 'guest',

  components: {
    LoginWithGithub
  },

  metaInfo () {
    return { title: this.$t('login') }
  },

  data: () => ({
    form: new Form({
      email: '',
      password: ''
    }),
    remember: false
  }),

  methods: {
    async login () {
      // Submit the form.
      const { data } = await this.form.post('/api/login')

      // Save the token.
      this.$store.dispatch('auth/saveToken', {
        token: data.token,
        remember: this.remember
      })

      // Fetch the user.
      await this.$store.dispatch('auth/fetchUser')

      // Redirect home.
      this.$router.push({ name: 'home' })
    }
  }
}
</script>
