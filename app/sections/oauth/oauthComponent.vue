<template>
    <v-card flat>
        <v-layout row wrap mb-0 mt-0 ml-2 mr-2 pt-3>
            <v-flex xs12 pb-3 v-if="oauth_id.oauth_id==null&&oauth_id.oauth_id==null">
                <v-select
                    outlined
                    dense
                    hide-details
                    label="Association id"
                    item-text="custom"
                    item-value="id"
                    :items="association_items"
                    v-model="association.id"
                ></v-select>
            </v-flex>

            <v-flex xs6 pr-2 pb-3>
                <v-text-field
                    outlined
                    dense
                    hide-details
                    readonly
                    label="Oauth id"
                    v-model="form.id"
                ></v-text-field>
            </v-flex>
            <v-flex xs6 pl-2 pr-2 pb-3>
                <v-text-field
                    outlined
                    dense
                    hide-details
                    label="Name"
                    v-model="form.name"
                ></v-text-field>
            </v-flex>

            <v-flex xs6 pr-2 pb-3>
                <v-text-field
                    outlined
                    dense
                    hide-details
                    label="Client id"
                    v-model="form.client_id"
                ></v-text-field>
            </v-flex>
            <v-flex xs6 pl-2>
                <v-text-field
                    outlined
                    dense
                    hide-details
                    label="Client secret"
                    v-model="form.client_secret"
                ></v-text-field>
            </v-flex>

            <v-flex xs12>
                <v-text-field
                    outlined
                    dense
                    hide-details="auto"
                    label="Redirect uris"
                    :hint="hint_redirect_uris ? 'The separator is ;' : ''"
                    append-icon="mdi-help-circle"
                    @click:append="hint_redirect_uris=!hint_redirect_uris"
                    v-model="form.redirect_uris"
                ></v-text-field>
            </v-flex>
        </v-layout>

        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
                @click="submit()"
            >
                Salva
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
module.exports = {
    data:function(){
        return{
            routes:[],
            association:{
                id:null
            },
            form:{
                id:null,
                name:null,
                client_id:null,
                client_secret:null,
                redirect_uris:null,
                hosts:null,
            },
            hint_redirect_uris:false,
            association_items:[],
        }
    },
    props:["oauth_id"],
    watch:{
        oauth_id:function(){
            this.loadData()
            this.getConsumers()
        }
    },
    methods: {
        getConsumers:function(){
            var self=this
            Utils.apiCall("get", "/kong/consumers")
            .then(function (response) {
                for(var i=0;i<response.data.data.length;i++){
                    response.data.data[i].custom=response.data.data[i].username + " ["+response.data.data[i].id+ "]"
                }
                self.association_items=response.data.data
            })
        },
        submit:function(){
            var self=this

            var tmp_redirect_uris=[]
            if(this.form.redirect_uris!=null){
                if(this.form.redirect_uris.includes(";")){
                    var tmp= this.form.redirect_uris.split(";")
                    for(var i=0;i<tmp.length;i++){
                        if(tmp[i]!='') tmp_redirect_uris.push(tmp[i])
                    }
                }else{
                    if(this.form.redirect_uris!=''){
                        tmp_redirect_uris.push(this.form.redirect_uris)
                    }
                }
            }


            var params={
                id: this.form.id,
                name: this.form.name,
                redirect_uris: tmp_redirect_uris,
                client_id:self.form.client_id,
                client_secret:self.form.client_secret,
            }
            if(self.oauth_id.oauth_id==null){
                params.consumer={}
                params.consumer.id=self.association.id
            }

            Utils.apiCall("post", "/kong/oauth",params)
            .then(function (response) {
                if(response!=undefined){
                    Swal.fire({
                        type: 'success',
                        title: self.oauth_id.oauth_id==null ? 'New oauth create' : 'Oauth updated',
                        text: self.oauth_id.oauth_id==null ? 'New oauth create' : 'Oauth updated',
                    }).then(function(result) {
                        self.$emit('close-modal',true)
                    })
                }
            });
        },
        loadData:function(){
            var self = this
            if(self.oauth_id.oauth_id!=null){
                var params={
                    id:self.oauth_id.oauth_id
                }
                Utils.apiCall("get", "/kong/oauth",params)
                .then(function (response) {
                    console.log(response)
                    self.form.id=response.data.id
                    self.form.name=response.data.name
                    if(response.data.redirect_uris!=null) {
                        if(response.data.redirect_uris.length==1){
                            self.form.redirect_uris=response.data.redirect_uris.toString()
                        }else{
                            self.form.redirect_uris=response.data.redirect_uris.join(";")
                        }
                    }
                    self.form.client_id=response.data.client_id
                    self.form.client_secret=response.data.client_secret
                });
            }else{
                self.form.id=null
                self.form.name=null
                self.form.redirect_uris=null
                self.form.client_secret=null
                self.form.client_id=null
            }
        },
    },
    created:function() {
        this.loadData()
    },
}
</script>
