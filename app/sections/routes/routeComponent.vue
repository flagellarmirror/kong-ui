<template>
    <v-card flat>
        <v-layout row wrap mb-0 mt-0 ml-2 mr-2 pt-3>
            <v-flex xs4 pr-2 pb-3>
                <v-text-field
                    outlined
                    dense
                    hide-details
                    readonly
                    label="Route id"
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
                    hide-details="auto"
                    label="Hosts"
                    :hint="hint_hosts ? 'The separator is ;' : ''"
                    append-icon="mdi-help-circle"
                    @click:append="hint_hosts=!hint_hosts"
                    v-model="form.hosts"
                ></v-text-field>
            </v-flex>
            <v-flex xs4 pl-2 pr-2>
                <v-select
                    outlined
                    dense
                    hide-details
                    label="Methods"
                    multiple
                    :items="methods_items"
                    v-model="form.methods"
                ></v-select>
            </v-flex>
            <v-flex xs4 pl-2>
                <v-text-field
                    outlined
                    dense
                    hide-details="auto"
                    label="Paths"
                    :hint="hint_paths ? 'The separator is ;' : ''"
                    append-icon="mdi-help-circle"
                    @click:append="hint_paths=!hint_paths"
                    v-model="form.paths"
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
                name:null,
                protocols:null,
                hosts:null,
                methods:null,
                paths:null,
            },
            hint_hosts:false,
            hint_paths:false,
            methods_items:[
                "GET",
                "POST",
                "DELETE",
                "PATCH",
                "PUT"
            ],
            protocols_items:[
                "http",
                "https"
            ],
        }
    },
    props:["service_id"],
    watch:{
        service_id:function(){
            console.log("cambio")
            this.loadData()
        }
    },
    methods: {
        submit:function(){
            var self=this

            var tmp_hosts=[]
            var tmp_paths=[]
            if(this.form.hosts!=null){
                if(this.form.hosts.includes(";")){
                    var tmp= this.form.hosts.split(";")
                    for(var i=0;i<tmp.length;i++){
                        if(tmp[i]!='') tmp_hosts.push(tmp[i])
                    }
                }else{
                    if(this.form.hosts!=''){
                        tmp_hosts.push(this.form.hosts)
                    }
                }
            }

            if(this.form.paths!=null){
                if(this.form.paths.includes(";")){
                    var tmp=this.form.paths.split(";")
                    for(var i=0;i<tmp.length;i++){
                        if(tmp[i]!='') tmp_paths.push(tmp[i])
                    }
                }else{
                    if(this.form.paths!=''){
                        tmp_paths.push(this.form.paths)
                    }
                }
            }


            var params={
                id: this.form.id,
                name: this.form.name,
                protocols: this.form.protocols,
                hosts: tmp_hosts,
                methods: this.form.methods,
                paths: tmp_paths,
                service: this.service_id
            }

            Utils.apiCall("post", "/kong/routes",params)
            .then(function (response) {
                if(response!=undefined){
                    Swal.fire({
                        type: 'success',
                        title: self.service_id==null ? 'New route create' : 'Route updated',
                        text: self.service_id==null ? 'New route create' : 'Route updated',
                    }).then(function(result) {
                        self.$emit('close-modal',true)
                    })
                }
            });
        },
        loadData:function(){
            var self = this
            if(self.service_id!=null){
                var params={
                    id:self.service_id
                }
                Utils.apiCall("get", "/kong/routes",params)
                .then(function (response) {
                    console.log(response)
                    self.form.id=response.data.id
                    self.form.name=response.data.name
                    if(response.data.methods!=null) self.form.methods=response.data.methods
                    if(response.data.hosts!=null) {
                        if(response.data.hosts.length==1){
                            self.form.hosts=response.data.hosts.toString()
                        }else{
                            self.form.hosts=response.data.hosts.join(";")
                        }
                    }
                    if(response.data.protocols!=null) self.form.protocols=response.data.protocols
                    if(response.data.paths!=null) {
                        if(response.data.paths.length==1){
                            self.form.paths=response.data.paths.toString()
                        }else{
                            self.form.paths=response.data.paths.join(";")
                        }
                    }
                });
            }
        },
    },
    created:function() {
        this.loadData()
    },
}
</script>
