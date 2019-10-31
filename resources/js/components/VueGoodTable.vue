<template>
  <vue-good-table v-if="rows && rows.length" 
    :columns="columns" 
    :rows="rows"
    @on-row-click="onRowClick"
    :search-options="{ 
      enabled: true, 
      trigger: '@keyup', 
      skipDiacritics: true,
      placeholder: 'Search this table'
    }"
    :pagination-options="{
          enabled: true,
          mode: 'records',
          perPage: 5,
          position: 'bottom',
          perPageDropdown: [5, 10, 15],
          dropdownAllowAll: false,
          nextLabel: 'Next',
          prevLabel: 'Prev',
          rowsPerPageLabel: 'Rows per page',
          ofLabel: 'Of',
          pageLabel: 'Page', // for 'pages' mode
          allLabel: 'All',
      }"
      :line-numbers="true"
      theme="black-rhino"
      >
  <div slot="table-actions">

          <!-- Action Buttons -->
          <button @click="btnSortBy('price')" class="btn btn-sm btn-light">Sort By Price</button>
          <button @click="btnSortBy('finished')" class="btn btn-sm btn-primary">Sort By State</button>
          <button @click="showNewItemModal()" class="btn btn-sm btn-outline-success mr-1">Add New Package</button>
          
          <!-- Modal Add New Package -->  
          <sweet-modal ref="new_package" modal-theme="dark" overlay-theme="dark">
            Add <b>New</b>-<i>Package</i>.
            <br /><br />
            <div class="container">
              <div class="row">
                <div class="input-group input-group-sm mb-4 col-md-12">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Tracking Number</span>
                  </div>
                  <input v-model="package_tracking" type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" pattern=".{4,40}" required>
                </div>
                <div class="input-group input-group-sm mb-4 col-md-6">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Name</span>
                  </div>
                  <input v-model="package_name" type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" required>
                </div>
                <div class="input-group input-group-sm mb-4 col-md-6">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Weight</span>
                  </div>
                  <input v-model="package_weight" type="text" id="new_weight_input" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" pattern=".{1,6}" step="0.01" required>
                </div>
              </div>
              <div class="row">
                <div class="input-group input-group-sm mb-3 col-md-6">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Price</span>
                  </div>
                  <input v-model="package_price" type="text" id="new_price_input" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" pattern=".{1,8}" step="0.01" required>
                </div>
                <div class="input-group input-group-sm mb-3 col-md-6">
                  <button @click="addNewPackage({package_tracking,package_name,package_weight,package_price})" class="btn btn-sm btn-outline-success">Save</button>
                </div>
              </div>
            </div>
            
            
          </sweet-modal>

          <!-- Success New Package Added Modal -->
          <sweet-modal ref="state_200" icon="success" modal-theme="dark" overlay-theme="dark">
            Package Added Successfuly.
          </sweet-modal>

          <!-- Warning Item Delete Modal -->
          <sweet-modal icon="warning" ref="state_yesno" title="Confirm Deleting Package." modal-theme="dark" overlay-theme="dark">
            <div class="row">
              <div class="col-md-12">
                Are You Sure To Delete Package?
              </div>
            </div>  
            <div class="row mt-3">
              <div class="col-md-12">
                <div class="btn-group" role="group" aria-label="">
                  <button type="button" v-on:click="deleteRowItem(true)" class="btn btn-outline-success">Sure</button>
                  <button type="button" v-on:click="deleteRowItem(false)" class="btn btn-outline-warning">Nah</button>
                </div>
              </div>
            </div>
          </sweet-modal>
          
  </div>
    <template slot="table-row" slot-scope="props">

      <!-- Show Edit/Delete Button - Accessing Delete Button with name="deleteRow" -->
      <span v-if="props.column.field == 'actions'">
          <div class="btn-group" role="group" aria-label="Basic example">
              <button :id="props.row.id" name="syncbtn" class="btn btn-sm btn-outline-dark">Sync</button>
              <button class="btn btn-sm btn-outline-primary">Edit</button>
              <button name="deleteRow" class="btn btn-sm btn-outline-danger">Delete</button>
          </div>  
      </span>

      <!-- Add Automatic Tracking URL -->
      <span v-else-if="props.column.field == 'tracking'">
          <a :href="'https://t.17track.net/en#nums='+ props.row.tracking" style="font-weight: color: brown;" target="_blank">{{props.row.tracking}}</a> 
      </span>

      <!-- Add Functionality Pending/Finished -->
      <span v-else-if="props.column.field == 'finished'">
          <button name="stateChangeBtn" :class="props.row.finished == 0 ? 'btn btn-sm btn-warning' : 'btn btn-sm btn-success'">{{ props.row.finished == 0 ? 'False' : 'True' }}</button> 
      </span>

      <span v-else-if="props.column.field == 'weight'">
          <span style="font-weight: bold; color: blue;">{{props.row.weight}} KG</span> 
      </span>

      <!-- Syncronise Takeout Price from INEX.GE -->
      <span v-else-if="props.column.field == 'takeout'">
          <h5>$ 
            <span class="badge badge-light">{{parseFloat(props.row.takeout).toFixed(2)}}</span>
          </h5>
      </span>

      <!-- Total Price Counter -->
      <span v-else-if="props.column.field == 'totalprice'">
        <h5><span class="badge badge-light">$ {{ parseFloat(props.row.totalprice).toFixed(2) }}</span></h5>
      </span>
      <!-- Item Price -->
      <span v-else-if="props.column.field == 'price'">
          <span style="font-weight: bold; color: green;">$ {{props.row.price}}</span> 
      </span>

      <!-- Else Show All Rows -->
      <span v-else>
        {{props.formattedRow[props.column.field]}}
      </span>

    </template>
  </vue-good-table>
</template>
<script>

export default {
  data(){
    return {
      package_tracking: "",
      package_name: "",
      package_weight: "",
      package_price: "",
      /** Deletable Item ID and Index in VueEllement */
      deletable_item: {
        id: 0,
        origIndex: 0,
      },
      columns: [
            {
              label: 'Tracking',
              field: 'tracking',
              sort: 'asc'
            },
            {
              label: 'Name',
              field: 'name',
              sort: 'asc'
            },
            {
              label: 'Weight',
              field: 'weight',
              sort: 'asc'
            },
            {
              label: 'Price',
              field: 'price',
              sort: 'asc'
            },
            {
              label: 'Takeout Price',
              field: 'takeout',
              sort: 'asc'
            },
            {
              label: 'Total Price',
              field: 'totalprice',
              sort: 'asc',
            },
            {
              label: 'State',
              field: 'finished',
              sort: 'asc',
              sortable: true,
            },
            {
              // Not Exists in Database Table Schema...
              label: 'Actions',
              field: 'actions'
            }
      ],
      rows: [
        // { id:1, name:"John", age: 20, createdAt: '2011-10-31',score: 0.03343 },
        // { id:2, name:"Jane", age: 24, createdAt: '2011-10-31', score: 0.03343 },
      ]
    };
  },
  methods: {

  /** Sort By Field Method */
  btnSortBy(field){
    axios.get("/packages/sortby/"+field)
      .then(resp => {
          this.rows = []

          resp.data.forEach((item,i) => {
              this.rows.push(item)
          });
      })
      .catch(error =>{
          this.loadPackages()
      })
  },

  /** Show New Item Add Modal */
  showNewItemModal(){
    this.$refs.new_package.open()

    /** Allow Only Numbers */
    $("#new_weight_input #new_price_input").on("keypress keyup blur",function(event) {
      $(this).val($(this).val().replace(/[^0-9\.]/g,''));
              if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                  event.preventDefault();
              }
    });
  },

  /** new Package Data Validator */
  validateNewPackageData(data){
     if(data.package_tracking.length > 40 || data.package_tracking.length < 8) return false
     else if(data.package_name.length === 0) return false
     else if(data.package_weight.length > 6 || data.package_weight.length < 1) return false
     else if(data.package_price.length > 8 || data.package_price.length < 1) return false
     else return true
  },

  /** Add New Package Using Axios */
  addNewPackage(data){
      if(this.validateNewPackageData(data))
      {
        axios({
          method: 'POST',
          url: '/packages/add',
          data: {
            tracking: data.package_tracking,
            name: data.package_name,
            weight: data.package_weight,
            price: data.package_price
          }
        }).then(rs => {
          
          /**  add to the beginning of an array.  */
          this.rows.unshift(rs.data);

          /** Close New Package Modal */
          this.$refs.new_package.close()

          /** Open Modal Success */
          this.$refs.state_200.open()

          /** Close Modal After 2 Seconds */
          setTimeout(() => this.$refs.state_200.close(), 2000);

          /** Clear V-MODELs */
          this.package_tracking = ""
          this.package_name = ""
          this.package_weight = ""
          this.package_price = ""

        }).catch(err => {
            console.log(err)
        })
      }else {console.log("Package Not Added.")}
    
  },

  /** Delete Row Item From Database */
  deleteRowItem(bool){
    if(bool){
      /** Sending AXIOS Post Request To API */
      axios.post('/packages/remove/'+this.deletable_item.id)
              .then(r => {
                          /** Makes new array Which Doesn`t include Removed ROW using params.row.originalIndex */
                          const filteredRows = this.rows.filter((item,index) => index !== this.deletable_item.origIndex)
                          /** Assign this.rows -> filteredRows */
                          this.rows = filteredRows

                          /** Close Modal After 2 Seconds */
                          setTimeout(() => this.$refs.state_yesno.close(), 1000);
              }).catch(err => {
                    /** Close Modal After 2 Seconds */
                    setTimeout(() => this.$refs.state_yesno.close(), 1000);
              })
    } else 
      /** Close Modal After 0.7 Seconds */
      setTimeout(() => this.$refs.state_yesno.close(), 100);
  },

  /** Check if Server Alive */
  isOnline($url){
    var request;
      if(window.XMLHttpRequest)
          request = new XMLHttpRequest();
      else
          request = new ActiveXObject("Microsoft.XMLHTTP");
      try{
        request.open('GET', $url, false);
        request.send(); // there will be a 'pause' here until the response to come.
        // the object request will be actually modified
        if (request.status === 404) return false
          return true
      }
      catch(err){
        return false
      }
  },

  /* 
  *    Event Listener @on-row-click 
  *
  */
  onRowClick(params) {

      /* If Delete Button Was Pressed */
      if(params.event.target.name == 'deleteRow'){

          /** Take Deletable Item */
          this.deletable_item.id = params.row.id
          this.deletable_item.origIndex = params.row.originalIndex

          /** Open Modal YesNo */
          this.$refs.state_yesno.open()
      }

      /**  if Finish/Pending Button Was Clicked */
      else if(params.event.target.name == 'stateChangeBtn'){

          axios.post('/packages/state/update/'+params.row.id)
              .then(r => {
                  this.rows[params.row.originalIndex].finished = r.data
                  /**  this.rows[params.row.originalIndex].finished = 1 or 0 */
              }).catch(err => {

              })
      }
      /**  ReSync TakeOut From TrackerController */
      else if(params.event.target.name == 'syncbtn'){
          
          /** Make Fadeout */
          $("#"+params.event.target.id+"").fadeOut("slow",function() {
            $(this).text("Syncronising...").fadeIn();
          });

          if(this.isOnline('https://www.inex.ge')){
              axios.post('/api/track/'+params.row.tracking +'/on/'+params.row.finished)
                  .then(res => {

                  this.rows[params.row.originalIndex].takeout = res.data

                  axios.post('/packages/takeout/'+params.row.tracking+'/update/'+res.data)
                      .then(rs => {

                          
                          /** Add Done */
                          $("#"+params.event.target.id+"").fadeOut("slow",function() {
                            $(this).text("Done").fadeIn();
                          });
                          
                          /** Assign Update Of TotalPrice */
                          this.rows[params.row.originalIndex].totalprice = rs.data

                          /** TimeOut For Show Resync */
                          setTimeout(function() { $("#"+params.event.target.id+"").fadeOut("slow",function() {
                                $(this).text("ReSync").fadeIn();
                          });},1500);
                      }).catch(err => {
                        $("#"+params.event.target.id+"").fadeOut("slow",function() {
                          $(this).text("Err").fadeIn();
                        });
                      })

                  }).catch(err => {
                    $("#"+params.event.target.id+"").fadeOut("slow",function() {
                      $(this).text("Err").fadeIn();
                    });
                  })
          }else{
            $("#"+params.event.target.id+"").fadeOut("slow",function() {
              $(this).text("Err").fadeIn();
            });
          }
      }//PARAMS
        // params.row - row object  
        // params.pageIndex - index of this row on the current page.
        // params.selected - if selection is enabled this argument 
        // indicates selected or not
        // params.event - click event
    },

    /** Gets Packages From Database */
    loadPackages(){
        axios.get('/packages').then(rs => {
            this.rows = []
            rs.data.forEach((item,i) => {
                this.rows.push(item)
            });
            /**   Using FOR Loop 
            **      for (var i = 0; i < rs.data.length; i++) {
            **         this.rows.push(rs.data[i])
            **      }
            */
        })

        setTimeout(function () { this.loadPackages() }.bind(this), 300000)
    }
  },
  /** When Item was Mounted - get Packages from DB */
  mounted(){
      this.loadPackages()
  },
};
</script>
