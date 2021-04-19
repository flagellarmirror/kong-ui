<template>
    <v-dialog max-width="1400" persistent :value="true">
        <v-card>
            <v-toolbar
                dense
                color="blue-grey"
                dark
            >
                <v-toolbar-title>
                    {{modalTitle}}
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn
                    icon
                    @click="closeModal(false)"
                >
                    <v-icon>mdi-close</v-icon>
                </v-btn>

                <template v-slot:extension>
                    <v-tabs
                        v-model="tab"
                        dark
                        fixed-tabs
                    >
                        <v-tabs-slider color="yellow"></v-tabs-slider>
                        <v-tab
                            v-for="item in tabs"
                            :key="item"
                        >
                            {{ item }}
                        </v-tab>
                        <v-tab v-if="item!=null">
                            <v-menu
                                v-if="more_route.length"
                                bottom
                                left
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                    text
                                    class="align-self-center mr-4"
                                    v-bind="attrs"
                                    v-on="on"
                                    >
                                    routes
                                    <v-icon right>
                                        mdi-menu-down
                                    </v-icon>
                                    </v-btn>
                                </template>

                                <v-list class="grey lighten-3">
                                    <v-list-item
                                    v-for="item in more_route"
                                    :key="item"
                                    @click="selectRoute(item)"
                                    >
                                    {{ item.custom }}
                                    </v-list-item>
                                </v-list>
                            </v-menu>
                        </v-tab>
                        <v-tab v-if="item!=null">
                            <v-menu
                                v-if="more.length"
                                bottom
                                left
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                    text
                                    class="align-self-center mr-4"
                                    v-bind="attrs"
                                    v-on="on"
                                    >
                                    plugins
                                    <v-icon right>
                                        mdi-menu-down
                                    </v-icon>
                                    </v-btn>
                                </template>

                                <v-list class="grey lighten-3">
                                    <v-list-item
                                    v-for="item in more"
                                    :key="item"
                                    @click="selectPlugin(item)"
                                    >
                                    {{ item.custom }}
                                    </v-list-item>
                                </v-list>
                            </v-menu>
                        </v-tab>
                    </v-tabs>
                </template>

            </v-toolbar>

            <!-- TABS -->
            <service-component @close-modal="closeModal($event)" :service_id="item" v-show="tab==0"></service-component>

            <route-component :route_id="route" v-show="tab==1"></route-component>

            <plugin-component @close-modal="closeModal($event)" :plugin_id="plugin" v-show="tab==2"></plugin-component>
        </v-card>
    </v-dialog>
</template>

<script>
module.exports={
    data:function() {
        return{
            routes:[],
            plugins:[],
            plugin:{
                plugin_id:null,
                origin:'service',
                origin_id:this.item
            },
            tabs:[
                'service'
            ],
            more:[{custom:'add plugin'}],
            more_route:[{custom:'add route'}],
            tab: null,
            route:{
                route_id:null,
                origin:'service',
                origin_id:this.item
            },
        }
    },
    props:['item'],
    computed:{
        modalTitle(){
            if(this.item!=null){
                return 'Modify service: '+this.item
            }else{
                return "Create service"
            }

        },
    },
    methods: {
        selectRoute:function(route){
            console.log(route.id)
            var tmp={
                route_id: route.id!=undefined ? route.id : null,
                origin:'service',
                origin_id:this.item
            }
            this.route=tmp
            this.tab=1
        },
        selectPlugin:function(plugin){
            console.log(plugin.id)
            var tmp={
                plugin_id: plugin.id!=undefined ? plugin.id : null,
                origin:'service',
                origin_id:this.item
            }
            this.plugin=tmp
            this.tab=2
        },
        closeModal:function(refresh){
            this.$emit('close-modal',refresh)
        },
        loadData:function(){
            if(this.item!=null){
                this.getRoutes()
                this.getPlugins()
            }
        },
        getRoutes:function(){
            var self=this
            Utils.apiCall("get", "/kong/routes")
            .then(function (response) {
                var tmp=[]
                var tmp_routes=[{custom:'add plugin'}]
                for(var i=0;i<response.data.data.length;i++){
                    response.data.data[i].custom=response.data.data[i].name + " ["+response.data.data[i].id+ "]"
                    if(response.data.data[i].service!=null&&response.data.data[i].service.id==self.item){
                        tmp.push(response.data.data[i])
                        tmp_routes.push(response.data.data[i])
                    }
                }
                self.more_route=tmp_routes
                self.routes=tmp
            });
        },
        getPlugins:function(){
            var self=this
            Utils.apiCall("get", "/kong/plugins")
            .then(function (response) {
                var tmp=[]
                var tmp_plugins=[{custom:'add plugin'}]
                for(var i=0;i<response.data.data.length;i++){
                    response.data.data[i].custom=response.data.data[i].name + " ["+response.data.data[i].id+ "]"
                    if(response.data.data[i].service!=null&&response.data.data[i].service.id==self.item){
                        tmp.push(response.data.data[i])
                        tmp_plugins.push(response.data.data[i])
                    }
                }
                self.more=tmp_plugins
                self.plugins=tmp
            });
        },
    },
    created:function() {
        this.loadData()
    },
    components:{
        'route-component': httpVueLoader('./../routes/routeComponent.vue' + '?v=' + new Date().getTime()),
        'service-component': httpVueLoader('./serviceComponent.vue' + '?v=' + new Date().getTime()),
        'plugin-component': httpVueLoader('./../plugins/pluginComponent.vue' + '?v=' + new Date().getTime()),
    }
}
</script>
