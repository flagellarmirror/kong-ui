<template>
    <div>

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
        }
    },
    methods: {
        openRouteModal:function(id){
            this.select=id
            this.routeModal=true
        },
        closeRouteModal:function(event){
            this.routeModal=false
            this.select=null
            if(event) this.$refs.routes.getRoutes()
        },
    },
    created:function() {
    },
    components:{
        'route-modal': httpVueLoader('./routeModal.vue' + '?v=' + new Date().getTime()),
        'routes-table': httpVueLoader('./routesTable.vue' + '?v=' + new Date().getTime())
    }
}
</script>
