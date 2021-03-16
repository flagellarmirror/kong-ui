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
            :item="{hide_link:false}"
            @open-route-modal="openRouteModal($event)"
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
            if(index!=undefined){
                this.select=id
            }
            this.routeModal=true
        },
        closeRouteModal:function(event){
            this.routeModal=false
            this.select=null
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
