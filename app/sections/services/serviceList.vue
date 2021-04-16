<template>
    <div>

        <!-- Service modal -->
        <service-modal
            v-if="serviceModal"
            @close-modal="closeServiceModal($event)"
            :item="select"
        ></service-modal>

        <services-table
            ref="services"
            :item="select"
            @event="openServiceModal($event)"
        ></services-table>

    </div>
</template>

<script>
module.exports={
    data:function(){
        return{
            serviceModal: false,
            select:null,
            services:[],
        }
    },
    methods: {
        getServices:function(){
            var self=this
            Utils.apiCall("get", "/kong/services")
            .then(function (response) {
                self.services=response.data.data
            });
        },
        openServiceModal:function(service_id){
            this.select=service_id
            this.serviceModal=true
        },
        closeServiceModal:function(event){
            this.serviceModal=false
            this.select=null
            if(event) this.$refs.services.getServices()
        },
    },
    created:function() {
        this.getServices()
    },
    components:{
        'services-table': httpVueLoader('./servicesTable.vue' + '?v=' + new Date().getTime()),
        'service-modal': httpVueLoader('./serviceModal.vue' + '?v=' + new Date().getTime())
    }
}
</script>
