<template>
    <v-dialog max-width="1400" persistent :value="true">
        <v-card>
            <v-toolbar
                dense
                color="blue"
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
            <service-component :service_id="item" v-show="tab==0"></service-component>

            <template v-if="tab==1">
                <v-layout row wrap mb-0 mt-0 ml-2 mr-2 pt-3>
                    <v-flex xs12>
                        <v-select
                            outlined
                            dense
                            hide-details
                            label="Select route"
                            :items="routes"
                            item-text="custom"
                            item-value="id"
                            v-model="route"
                        ></v-select>
                    </v-flex>
                </v-layout>
                <route-component :service_id="route" v-show="tab==1"></route-component>
            </template>
        </v-card>
    </v-dialog>
</template>

<script>
module.exports={
    data:function() {
        return{
            routes:[],
            plugins:[],
            tabs:[
                'services', 'routes', 'plugins'
            ],
            tab: null,
            route:null,
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
                for(var i=0;i<response.data.data.length;i++){
                    response.data.data[i].custom=response.data.data[i].name + " ["+response.data.data[i].id+ "]"
                    if(response.data.data[i].service!=null&&response.data.data[i].service.id==self.item){
                        tmp.push(response.data.data[i])
                    }
                }
                if(tmp.length==0){
                    for(var i=0;i<self.tabs.length;i++){
                        if(self.tabs[i]=='routes'){
                            self.tabs.splice(i,1)
                        }
                    }
                }
                self.routes=tmp
            });
        },
        getPlugins:function(){
            var self=this
            Utils.apiCall("get", "/kong/plugins")
            .then(function (response) {
                var tmp=[]
                for(var i=0;i<response.data.data.length;i++){
                    response.data.data[i].custom=response.data.data[i].name + " ["+response.data.data[i].id+ "]"
                    if(response.data.data[i].service!=null&&response.data.data[i].service.id==self.item){
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
                //TODO: Add plugin component
            });
        },
    },
    created:function() {
        this.loadData()
    },
    components:{
        'route-component': httpVueLoader('./../routes/routeComponent.vue' + '?v=' + new Date().getTime()),
        'service-component': httpVueLoader('./serviceComponent.vue' + '?v=' + new Date().getTime())
    }
}
</script>
