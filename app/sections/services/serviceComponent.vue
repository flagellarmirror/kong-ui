<template>
    <v-card flat>
        <v-layout row wrap mb-0 mt-0 ml-2 mr-2 pt-3>
            <v-flex xs4 pr-2 pb-3>
                <v-text-field
                    outlined
                    dense
                    hide-details
                    readonly
                    label="Service id"
                    v-model="form.id"
                ></v-text-field>
            </v-flex>
            <v-flex xs4 pl-2 pr-2 pb-3>
                <v-text-field
                    outlined
                    dense
                    hide-details
                    label="Name"
                    v-model="form.name"
                ></v-text-field>
            </v-flex>
            <v-flex xs4 pl-2 pb-3>
                <v-text-field
                    outlined
                    dense
                    hide-details
                    label="Protocol"
                    v-model="form.protocol"
                ></v-text-field>
            </v-flex>

            <v-flex xs4 pr-2>
                <v-text-field
                    outlined
                    dense
                    hide-details
                    label="Host"
                    v-model="form.host"
                ></v-text-field>
            </v-flex>
            <v-flex xs4 pl-2 pr-2>
                <v-text-field
                    outlined
                    dense
                    hide-details
                    label="Port"
                    v-model="form.port"
                ></v-text-field>
            </v-flex>
            <v-flex xs4 pl-2>
                <v-text-field
                    outlined
                    dense
                    hide-details
                    label="Path"
                    v-model="form.path"
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
            form:{
                id:null,
                name:null,
                protocol:null,
                host:null,
                port:null,
                path:null,
            },
        }
    },
    props:["service_id"],
    watch:{
        service_id:function(){
            this.loadData()
        }
    },
    methods: {
        loadData:function(){
            var self=this
            if(this.service_id!=null){
                var params={
                    id:this.service_id
                }
                Utils.apiCall("get", "/kong/services",params)
                .then(function (response) {
                    self.form.id=response.data.id
                    self.form.name=response.data.name
                    self.form.protocol=response.data.protocol
                    self.form.host=response.data.host
                    self.form.port=response.data.port
                    self.form.path=response.data.path
                });
            }
        },
        submit:function(){
            var self=this
            Utils.apiCall("post", "/kong/services",this.form)
            .then(function (response) {
                // PROBLEMA APICALL LA CHIAMATA FALLISCHE MA IL CODICE VIENE ESEGUITO
                if(response!=undefined){
                    Swal.fire({
                        type: 'success',
                        title: self.service_id.service_id==null ? 'New service create' : 'Service updated',
                        text: self.service_id.service_id==null ? 'New service create' : 'Service updated',
                    }).then(function(result) {
                        self.$emit('close-modal',true)
                    })
                }
            });
        },
    },

    created:function() {
        this.loadData()
    },
}
</script>
