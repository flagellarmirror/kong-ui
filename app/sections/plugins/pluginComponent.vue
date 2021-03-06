<template>
    <v-card flat>
        <v-layout row wrap mb-0 mt-0 ml-2 mr-2 pt-3>
            <v-flex xs12 pb-3>
                <v-select
                    outlined
                    dense
                    hide-details
                    :items="plugins_items"
                    v-model="form.name"
                    label="Plugin type"
                ></v-select>
            </v-flex>

            <!-- Oauth2 plugin -->
            <v-flex xs4 pr-2 pb-3>
                <v-text-field
                    outlined
                    dense
                    hide-details
                    readonly
                    label="Plugin id"
                    v-model="form.id"
                ></v-text-field>
            </v-flex>
            <v-flex xs4 pl-2 pr-2 pb-3>
                <v-checkbox
                    class="mt-1"
                    outlined
                    dense
                    hide-details
                    label="Enabled"
                    v-model="form.enabled"
                ></v-checkbox>
            </v-flex>
            <v-flex xs4 pl-2 pb-3>
                <v-select
                    outlined
                    dense
                    hide-details
                    label="Protocols"
                    multiple
                    :items="protocols_items"
                    v-model="form.protocols"
                ></v-select>
            </v-flex>

            <v-flex xs4 pr-2>
                <v-text-field
                    outlined
                    dense
                    hide-details
                    label="Token expiration"
                    v-model="form.token_expiration"
                ></v-text-field>
            </v-flex>
            <v-flex xs4 pl-2 pr-2>
                <v-text-field
                    outlined
                    dense
                    hide-details
                    label="Provision key"
                    v-model="form.provision_key"
                ></v-text-field>
            </v-flex>
            <v-flex xs4 pl-2>
                <v-text-field
                    outlined
                    dense
                    hide-details="auto"
                    label="Scopes"
                    :hint="hint_scopes ? 'The separator is ;' : ''"
                    append-icon="mdi-help-circle"
                    @click:append="hint_scopes=!hint_scopes"
                    v-model="form.scopes"
                ></v-text-field>
            </v-flex>

            <v-flex xs3 pr-2>
                <v-checkbox
                    outlined
                    dense
                    hide-details
                    label="Reuse refresh token"
                    v-model="form.reuse_refresh_token"
                ></v-checkbox>
            </v-flex>
            <v-flex xs3 pl-2 pr-2>
                <v-checkbox
                    outlined
                    dense
                    hide-details
                    label="Mandatory scope"
                    v-model="form.mandatory_scope"
                ></v-checkbox>
            </v-flex>
            <v-flex xs3 pl-2 pr-2>
                <v-checkbox
                    outlined
                    dense
                    hide-details
                    label="Enable client credentials"
                    v-model="form.enable_client_credentials"
                ></v-checkbox>
            </v-flex>
            <v-flex xs3 pl-2>
                <v-checkbox
                    outlined
                    dense
                    hide-details
                    label="Enable authorization code"
                    v-model="form.enable_authorization_code"
                ></v-checkbox>
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
            form:{
                id:null,
                name:null,
                protocols:null,
                reuse_refresh_token:false,
                mandatory_scope:true,
                token_expiration:null,
                enable_client_credentials:false,
                enable_authorization_code:true,
                provision_key:null,
                enabled:true,
                scopes:null,
            },
            hint_scopes:false,
            plugins_items:[
                "oauth2",
            ],
            protocols_items:[
                "http",
                "https"
            ],
        }
    },
    props:["plugin_id"],
    watch:{
        plugin_id:function(){
            this.loadData()
        }
    },
    methods: {
        submit:function(){
            var self=this

            var tmp_scopes=[]
            if(this.form.scopes!=null){
                if(this.form.scopes.includes(";")){
                    var tmp= this.form.scopes.split(";")
                    for(var i=0;i<tmp.length;i++){
                        if(tmp[i]!='') tmp_scopes.push(tmp[i])
                    }
                }else{
                    if(this.form.scopes!=''){
                        tmp_scopes.push(this.form.scopes)
                    }
                }
            }

            var params={
                id: this.form.id,
                name: this.form.name,
                protocols: this.form.protocols,
                enabled:this.form.enabled,
                config:{
                    scopes: tmp_scopes,
                    reuse_refresh_token: this.form.reuse_refresh_token,
                    mandatory_scope: this.form.mandatory_scope,
                    token_expiration: this.form.token_expiration,
                    enable_client_credentials: this.form.enable_client_credentials,
                    enable_authorization_code: this.form.enable_authorization_code,
                    provision_key: this.form.provision_key
                }
            }

            Utils.apiCall("post", "/kong/plugins",params)
            .then(function (response) {
                if(response!=undefined){
                    Swal.fire({
                        type: 'success',
                        title: self.plugin_id==null ? 'New plugin create' : 'Plugin updated',
                        text: self.plugin_id==null ? 'New plugin create' : 'Plugin updated',
                    }).then(function(result) {
                        self.$emit('close-modal',true)
                    })
                }
            });
        },
        loadData:function(){
            var self = this
            if(self.plugin_id!=null){
                var params={
                    id:self.plugin_id
                }
                Utils.apiCall("get", "/kong/plugins",params)
                .then(function (response) {
                    console.log(response)
                    self.form.id=response.data.id
                    self.form.name=response.data.name
                    if(response.data.protocols!=null) self.form.protocols=response.data.protocols
                    if(response.data.config.scopes!=null) {
                        if(response.data.config.scopes.length==1){
                            self.form.scopes=response.data.config.scopes.toString()
                        }else{
                            self.form.scopes=response.data.config.scopes.join(";")
                        }
                    }
                    self.form.enabled=response.data.enabled
                    self.form.reuse_refresh_token=response.data.config.reuse_refresh_token
                    self.form.mandatory_scope=response.data.config.mandatory_scope
                    self.form.token_expiration=response.data.config.token_expiration
                    self.form.enable_client_credentials=response.data.config.enable_client_credentials
                    self.form.enable_authorization_code=response.data.config.enable_authorization_code
                    self.form.provision_key=response.data.config.provision_key
                });
            }
        },
    },
    created:function() {
        this.loadData()
    },
}
</script>
