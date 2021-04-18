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
                    </v-tabs>
                </template>

            </v-toolbar>

            <!-- TABS -->
            <plugin-component @close-modal="closeModal($event)" :plugin_id="{plugin_id:item,origin:null}" v-show="tab==0"></plugin-component>

            <route-component @close-modal="closeModal($event)" :route_id="route" v-show="tab==1"></route-component>

            <service-component @close-modal="closeModal($event)" :service_id="service" v-show="tab==2"></service-component>

        </v-card>
    </v-dialog>
</template>

<script>
module.exports={
    data:function() {
        return{
            service:null,
            route:null,
            tabs:[
                {
                    name:'plugin',
                    enabled:true,
                },
                {
                    name:'route',
                    enabled:true,
                },
                {
                    name:'service',
                    enabled:true,
                }
            ],
            tab: null,
        }
    },
    props:['item'],
    computed:{
        modalTitle(){
            if(this.item!=null){
                return 'Modify plugin: '+this.item
            }else{
                return "Create plugin"
            }

        },
    },
    methods: {
        closeModal:function(refresh){
            this.$emit('close-modal',refresh)
        },
        loadData:function(){
            if(this.item!=null){
                this.getPlugin()
            }
            else{
                this.tabs=[{
                    name:'plugin',
                    enabled:true,
                }]
            }
        },
        getPlugin:function(){
            var self=this
            var params={
                id: self.item
            }
            Utils.apiCall("get", "/kong/plugins",params)
            .then(function (response) {
                console.log(response)
                if(response.data.service!=null){
                    self.service=response.data.service.id
                }else{
                    for(var i=0;i<self.tabs.length;i++){
                        if(self.tabs[i].name=='service'){
                            self.tabs[i].enabled=false
                        }
                    }
                }
                if(response.data.route!=null){
                    self.route=response.data.route.id
                }else{
                    for(var i=0;i<self.tabs.length;i++){
                        if(self.tabs[i].name=='route'){
                            self.tabs[i].enabled=false
                        }
                    }
                }
            }).catch(function(){
                for(var i=0;i<self.tabs.length;i++){
                    if(self.tabs[i].name=='service'){
                        self.tabs[i].enabled=false
                    }
                    if(self.tabs[i].name=='route'){
                        self.tabs[i].enabled=false
                    }
                }
            })
        },
    },
    created:function() {
        this.loadData()
    },
    components:{
        'plugin-component': httpVueLoader('./pluginComponent.vue' + '?v=' + new Date().getTime()),
        'route-component': httpVueLoader('./../routes/routeComponent.vue' + '?v=' + new Date().getTime()),
        'service-component': httpVueLoader('./../services/serviceComponent.vue' + '?v=' + new Date().getTime())
    }
}
</script>
