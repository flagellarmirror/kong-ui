<template>
    <div>

        <!-- Route modal -->
        <route-modal
            v-if="routeModal"
            @close-modal="closeRouteModal($event)"
            :item="select"
        ></route-modal>

        <routes-table
            ref="routes"
            :item="{hide_link:false, hide_new_row:true}"
            @event="openRouteModal($event)"
        ></routes-table>

    </div>
</template>

<script>
module.exports={
    data:function(){
        return{
            routeModal: false,
            select:null,
            routes:[],
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
                    text: 'Route ID',
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
                    text: 'Hosts',
                    value: 'hosts',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Paths',
                    value: 'paths',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Methods',
                    value: 'methods',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Service ID',
                    value: 'service_id',
                    custom:{
                        type:"text"
                    }
                },
            ],
        }
    },
    methods: {
        openRouteModal:function(id){
            this.select={
                id:id
            }
            this.routeModal=true
        },
        closeRouteModal:function(event){
            console.log("quii")
            console.log(event)
            this.routeModal=false
            this.select=null
            if(event.event==undefined){ //Close modal
                if(event.refresh) this.$refs.routes.getRoutes()
            }
            // else if(event.event=='editRoute'){
            //     this.openRouteModal(event.data)
            // }
        },
    },
    created:function() {
    },
    components:{
        'route-modal': httpVueLoader('./RouteModal.vue' + '?v=' + new Date().getTime()),
        'routes-table': httpVueLoader('./../tables/routesTable.vue' + '?v=' + new Date().getTime())
    }
}
</script>
