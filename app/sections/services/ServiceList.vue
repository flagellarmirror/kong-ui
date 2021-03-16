<template>
    <div>

        <!-- Service modal -->
        <service-modal
            v-if="serviceModal"
            @close-modal="closeServiceModal($event)"
            :item="select"
        ></service-modal>

        <!-- Route modal -->
        <route-modal
            v-if="routeModal"
            @close-modal="closeRouteModal($event)"
            :item="select"
        ></route-modal>

        <!-- Plugin modal -->
        <plugin-modal
            v-if="pluginModal"
            @close-modal="closePluginModal($event)"
            :item="select"
        ></plugin-modal>

        <services-table
            ref="services"
            @open-service-modal="openServiceModal($event)"
        ></services-table>

    </div>
</template>

<script>
module.exports={
    data:function(){
        return{
            serviceModal: false,
            routeModal: false,
            pluginModal: false,
            select:null,
            services:[],
            headers: [
                {
                    text: 'Actions',
                    value: null,
                    align: 'center',
                    sortable: false,
                    custom:{
                        type:"btn"
                    }
                },
                {
                    text: 'Service ID',
                    align: 'start',
                    value: 'id',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Name',
                    value: 'name',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Host',
                    value: 'host',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Path',
                    value: 'path',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Protocol',
                    value: 'protocol',
                    custom:{
                        type:"text"
                    }
                },
            ],
        }
    },
    methods: {
        openServiceModal:function(id){
            this.select={
                hide_link:true,
                parent_id:null,
                id: id!=undefined ? id : null
            }
            this.serviceModal=true
        },
        closeServiceModal:function(event){
            this.serviceModal=false
            this.select=null
            if(event.event==undefined){ //Close modal
                if(event.refresh) this.$refs.services.getServices()
            }else if(event.event=='editRoute'){
                this.openRouteModal(event.data)
            }else if(event.event=='editPlugin'){
                console.log("arrivato")
                this.openPluginModal(event.data)
            }
        },
        openPluginModal:function(data){
            this.select=data
            this.pluginModal=true
        },
        closePluginModal:function(){
            this.pluginModal=false
            this.select=null
        },
        openRouteModal:function(data){
            this.select=data
            this.routeModal=true
        },
        closeRouteModal:function(event){
            this.routeModal=false
            this.select=null
            if(event.event==undefined){ //Close modal
                if(event.refresh) this.$refs.services.getServices()
            }else if(event.event=='editRoute'){
                // this.select=event.id
                // this.openRouteModal(event.id)
            }
        }
    },
    components:{
        'services-table': httpVueLoader('./../tables/servicesTable.vue' + '?v=' + new Date().getTime()),
        'service-modal': httpVueLoader('./ServiceModal.vue' + '?v=' + new Date().getTime()),
        'route-modal': httpVueLoader('./../routes/RouteModal.vue' + '?v=' + new Date().getTime()),
        'plugin-modal': httpVueLoader('./../plugins/PluginModal.vue' + '?v=' + new Date().getTime())
    }
}
</script>
