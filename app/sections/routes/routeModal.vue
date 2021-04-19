<template>
    <v-dialog max-width="1400" persistent :value="true">
        <v-card>
            <v-toolbar
                dense
                dark
                color="blue"
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
                        <v-tab
                            v-for="item in tabs"
                            :key="item"
                            :disabled="!item.enabled"
                        >
                            {{ item.name }}
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
            <route-component @close-modal="closeModal($event)" :route_id="{route_id:item,origin:null}" v-show="tab==0"></route-component>

            <service-component @close-modal="closeModal($event)" :service_id="service" v-show="tab==1"></service-component>

            <plugin-component @close-modal="closeModal($event)" :plugin_id="plugin" v-show="tab==2"></plugin-component>

        </v-card>
    </v-dialog>
</template>

<script>
module.exports={
    data:function() {
        return{
            plugins:[],
            plugin:{
                plugin_id:null,
                origin:'route',
                origin_id:this.item
            },
            tabs:[
                {
                    name:'route',
                    enabled:true,
                },
                {
                    name:'service',
                    enabled:true,
                }
            ],
            more:[{custom:'add plugin'}],
            tab: null,
            service:null,
        }
    },
    props:['item'],
    computed:{
        modalTitle(){
            if(this.item!=null){
                return 'Modify route: '+this.item
            }else{
                return "Create route"
            }

        },
    },
    methods: {
        selectPlugin:function(plugin){
            console.log(plugin.id)
            var tmp={
                plugin_id: plugin.id!=undefined ? plugin.id : null,
                origin:'route',
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
                this.getRoute()
                this.getPlugins()
            }else{
                this.tabs=[
                    {
                        name:'route',
                        enabled:true,
                    }
                ]
            }
        },
        getRoute:function(){
            var self=this
            var params={
                id: self.item
            }
            Utils.apiCall("get", "/kong/routes",params)
            .then(function (response) {
                if(response.data.service!=null){
                    self.service=response.data.service.id
                }else{
                    for(var i=0;i<self.tabs.length;i++){
                        if(self.tabs[i].name=='service'){
                            self.tabs[i].enabled=false
                        }
                    }
                }
            }).catch(function(){
                for(var i=0;i<self.tabs.length;i++){
                    if(self.tabs[i].name=='service'){
                        self.tabs[i].enabled=false
                    }
                }
            })
        },
        getPlugins:function(){
            var self=this
            Utils.apiCall("get", "/kong/plugins")
            .then(function (response) {
                var tmp=[]
                var tmp_plugins=[{custom:'add plugin'}]
                for(var i=0;i<response.data.data.length;i++){
                    response.data.data[i].custom=response.data.data[i].name + " ["+response.data.data[i].id+ "]"
                    if(response.data.data[i].route!=null&&response.data.data[i].route.id==self.item){
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
        'route-component': httpVueLoader('./routeComponent.vue' + '?v=' + new Date().getTime()),
        'service-component': httpVueLoader('./../services/serviceComponent.vue' + '?v=' + new Date().getTime()),
        'plugin-component': httpVueLoader('./../plugins/pluginComponent.vue' + '?v=' + new Date().getTime())
    }
}
</script>
