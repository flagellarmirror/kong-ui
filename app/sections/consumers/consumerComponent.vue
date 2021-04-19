<template>
    <v-card flat>
        <v-layout row wrap mb-0 mt-0 ml-2 mr-2 pt-3>
            <v-flex xs12 pb-3>
                <v-text-field
                    outlined
                    dense
                    hide-details
                    readonly
                    label="Cosumer id"
                    v-model="form.id"
                ></v-text-field>
            </v-flex>
            <v-flex xs6 pr-2 pb-3>
                <v-text-field
                    outlined
                    dense
                    hide-details
                    label="Username"
                    v-model="form.username"
                ></v-text-field>
            </v-flex>
            <v-flex xs6 pl-2 pb-3>
                <v-text-field
                    outlined
                    dense
                    hide-details
                    label="Custom id"
                    v-model="form.custom_id"
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
            form:{
                id:null,
                username:null,
                custom_id:null
            },
        }
    },
    props:["consumer_id"],
    watch:{
        consumer_id:function(){
            this.loadData()
        }
    },
    methods: {
        submit:function(){
            var self=this

            var params={
                id: this.form.id,
                username: this.form.username,
                custom_id:this.form.custom_id
            }

            Utils.apiCall("post", "/kong/consumers",params)
            .then(function (response) {
                if(response!=undefined){
                    Swal.fire({
                        type: 'success',
                        title: self.consumer_id.consumer_id==null ? 'New consumer create' : 'Consumer updated',
                        text: self.consumer_id.consumer_id==null ? 'New consumer create' : 'Consumer updated',
                    }).then(function(result) {
                        self.$emit('close-modal',true)
                    })
                }
            });
        },
        loadData:function(){
            var self = this
            if(self.consumer_id.consumer_id!=null){
                var params={
                    id:self.consumer_id.consumer_id
                }
                Utils.apiCall("get", "/kong/consumers",params)
                .then(function (response) {
                    console.log(response)
                    self.form.id=response.data.id
                    self.form.username=response.data.username
                    if(response.data.custom_id!=null) self.form.custom_id=response.data.custom_id
                });
            }else{
                self.form.id=null
                self.form.username=null
                self.form.custom_id=null
            }
        },
    },
    created:function() {
        this.loadData()
    },
}
</script>
