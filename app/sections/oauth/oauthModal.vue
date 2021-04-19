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
            <oauth-component @close-modal="closeModal($event)" :oauth_id="{oauth_id:item,origin:null}" v-show="tab==0"></oauth-component>

            <consumer-component @close-modal="closeModal($event)" :consumer_id="{consumer_id:consumer,origin:'oauth'}" v-show="tab==1"></consumer-component>

        </v-card>
    </v-dialog>
</template>

<script>

module.exports={
    data:function() {
        return{
            consumer:null,
            tabs:[
                {
                    name:'oauth2',
                    enabled:true,
                },
                {
                    name:'consumer',
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
                this.getConsumer()
            }
            else{
                this.tabs=[{
                    name:'oauth',
                    enabled:true,
                }]
            }
        },
        getConsumer:function(){
            var self=this
            Utils.apiCall("get", "/kong/oauth")
            .then(function (response) {
                console.log(response)
                for(var i=0;i<response.data.data.length;i++){
                    if(response.data.data[i].consumer!=null){
                        self.consumer=response.data.data[i].consumer.id
                        self.tabs[1].enabled=true
                        break
                    }
                }
            })
        },
    },
    created:function() {
        this.loadData()
    },
    components:{
        'consumer-component': httpVueLoader('./../consumers/consumerComponent.vue' + '?v=' + new Date().getTime()),
        'oauth-component': httpVueLoader('./oauthComponent.vue' + '?v=' + new Date().getTime())
    }
}
</script>
