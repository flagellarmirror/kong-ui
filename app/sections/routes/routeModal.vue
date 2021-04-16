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
                        >
                            {{ item }}
                        </v-tab>
                    </v-tabs>
                </template>

            </v-toolbar>

            <!-- TABS -->
            <route-component @close-modal="closeModal($event)" :route_id="item" v-show="tab==0"></route-component>

            <service-component @close-modal="closeModal($event)" :service_id="service" v-show="tab==1"></service-component>

            <template v-if="tab==2">
                <v-layout row wrap mb-0 mt-0 ml-2 mr-2 pt-3>
                    <v-flex xs12>
                        <v-select
                            outlined
                            dense
                            hide-details
                            label="Select plugin"
                            :items="plugins"
                            item-text="custom"
                            item-value="id"
                            v-model="plugin"
                        ></v-select>
                    </v-flex>
                </v-layout>
                <plugin-component @close-modal="closeModal($event)" :plugin_id="plugin" v-show="tab==2"></plugin-component>
            </template>

        </v-card>
    </v-dialog>
</template>

<script>
module.exports={
    data:function() {
        return{
            plugins:[],
            plugin:null,
            tabs:[
                'route', 'service', 'plugins'
            ],
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
        closeModal:function(refresh){
            this.$emit('close-modal',refresh)
        },
        loadData:function(){
            if(this.item!=null){
                this.getRoute()
                this.getPlugins()
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
                        if(self.tabs[i]=='service'){
                            self.tabs.splice(i,1)
                        }
                    }
                }
            }).catch(function(){
                for(var i=0;i<self.tabs.length;i++){
                    if(self.tabs[i]=='service'){
                        self.tabs.splice(i,1)
                    }
                }
            })
        },
        getPlugins:function(){
            var self=this
            Utils.apiCall("get", "/kong/plugins")
            .then(function (response) {
                var tmp=[]
                for(var i=0;i<response.data.data.length;i++){
                    response.data.data[i].custom=response.data.data[i].name + " ["+response.data.data[i].id+ "]"
                    if(response.data.data[i].route!=null&&response.data.data[i].route.id==self.item){
                        tmp.push(response.data.data[i])
                    }
                }
                if(tmp.length==0){
                    for(var i=0;i<self.tabs.length;i++){
                        if(self.tabs[i]=='plugins'){
                            self.tabs.splice(i,1)
                        }
                    }
                }
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
