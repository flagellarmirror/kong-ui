<template>
    <v-dialog max-width="1400" persistent :value="true">
        <v-card>
            <v-toolbar
                dense
                dark
                color="blue-grey"
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
                            :disabled="!item.enabled"
                        >
                            {{ item.name }}
                        </v-tab>
                    </v-tabs>
                </template>

            </v-toolbar>

            <!-- TABS -->
            <consumer-component @close-modal="closeModal($event)" :consumer_id="{consumer_id:item,origin:null}" v-show="tab==0"></consumer-component>

            <oauth-component @close-modal="closeModal($event)" :oauth_id="{oauth_id:oauth,origin:'consumer'}" v-show="tab==1"></oauth-component>

            <!-- <service-component @close-modal="closeModal($event)" :service_id="service" v-show="tab==2"></service-component> -->

        </v-card>
    </v-dialog>
</template>

<script>

module.exports={
    data:function() {
        return{
            oauth:null,
            tabs:[
                {
                    name:'consumer',
                    enabled:true,
                },
                {
                    name:'oauth2',
                    enabled:false,
                }
            ],
            tab: null,
        }
    },
    props:['item'],
    computed:{
        modalTitle(){
            if(this.item!=null){
                return 'Modify oauth: '+this.item
            }else{
                return "Create oauth"
            }

        },
    },
    methods: {
        closeModal:function(refresh){
            this.$emit('close-modal',refresh)
        },
        loadData:function(){
            if(this.item!=null){
                this.getOauth()
            }
            else{
                this.tabs=[{
                    name:'consumer',
                    enabled:true,
                }]
            }
        },
        getOauth:function(){
            var self=this
            Utils.apiCall("get", "/kong/oauth")
            .then(function (response) {
                console.log(response)
                for(var i=0;i<response.data.data.length;i++){
                    if(response.data.data[i].consumer!=null){
                        if(response.data.data[i].consumer.id==self.item){
                            self.oauth=response.data.data[i].id
                            self.tabs[1].enabled=true
                            break
                        }
                    }
                }
            })
        },
    },
    created:function() {
        this.loadData()
    },
    components:{
        'consumer-component': httpVueLoader('./consumerComponent.vue' + '?v=' + new Date().getTime()),
        'oauth-component': httpVueLoader('./../oauth/oauthComponent.vue' + '?v=' + new Date().getTime())
    }
}
</script>
