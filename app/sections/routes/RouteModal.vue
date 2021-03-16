<template>
    <v-dialog max-width="1400" persistent :value="true">
        <v-card>
            <v-toolbar
                dense
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
            </v-toolbar>

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

            <v-card class="ma-2" v-if="item.id!=null">
                <v-card-title class="pl-2 pr-0 pb-0 pt-0">
                    Link
                    <v-spacer></v-spacer>

                <v-select
                    dense
                    hide-details
                    :items="type_items"
                    v-model="type"
                ></v-select>
                </v-card-title>

                <v-data-table
                    :headers="headers"
                    :items="services"
                    :items-per-page="5"
                    class="elevation-1"
                    dense
                >
                    <template v-slot:header="{ props }">
                        <tr>
                            <template v-for="(head,i) in props.headers">
                                <th :key="i">
                                    <v-text-field
                                        v-if="head.custom.type=='text'"
                                        solo
                                        dense
                                        hide-details
                                    ></v-text-field>
                                    <v-btn
                                        v-if="head.custom.type=='btn'"
                                        icon
                                    >
                                    <v-icon>mdi-plus</v-icon>
                                    </v-btn>
                                </th>
                            </template>
                        </tr>
                    </template>
                    <template v-slot:item="{ item,index,headers }">
                        <tr>
                            <template v-for="(header,i) in headers">
                                <td :key="i" v-if="i==0" style="text-align:center">
                                    <v-btn icon>
                                        <v-icon>mdi-delete</v-icon>
                                    </v-btn>
                                    <v-btn
                                        icon
                                        @click="openServiceModal(index)"
                                    >
                                        <v-icon>mdi-pencil</v-icon>
                                    </v-btn>
                                </td>
                                <td :key="i" v-else>{{item[header['value']]}}</td>
                            </template>
                        </tr>
                    </template>
                </v-data-table>
            </v-card>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    @click="submit()"
                >
                    Salva
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
module.exports={
    data:function() {
        return{
            services:[],
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
            type:"services",
            type_items:[
                "services",
                "plugins",
                "consumers"
            ],
            headers: [
                {
                    text: 'Actions',
                    value: null,
                    align: 'center',
                    sortable: false,
                    custom:{
                        type:"btn"
                    }
                },
                {
                    text: 'Service ID',
                    align: 'start',
                    value: 'id',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Name',
                    value: 'name',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Host',
                    value: 'host',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Path',
                    value: 'path',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Protocol',
                    value: 'protocol',
                    custom:{
                        type:"text"
                    }
                },
            ],
        }
    },
    props:['item'],
    computed:{
        modalTitle(){
            if(this.item.id!=null){
                return 'Modify route: '+this.item.id
            }else{
                return "Create route"
            }

        },
    },
    methods: {
        closeModal:function(value,index,event){
            var tmp={
                event:event,
                refresh:value,
                id:index!=undefined ? this.routes[index].id : null
            }
            this.$emit('close-modal',tmp)
        },
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
                service: self.item.parent_id
            }

            Utils.apiCall("post", "/kong/routes",params)
            .then(function (response) {
                if(response!=undefined){
                    Swal.fire({
                        type: 'success',
                        title: self.item.id==null ? 'New route create' : 'Route updated',
                        text: self.item.id==null ? 'New route create' : 'Route updated',
                    }).then(function(result) {
                        self.closeModal(true)
                    })
                }
            });
        },
        loadData:function(){
            var self = this


            if(this.item.id!=null){
                var params={
                    id:this.item.id
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
                    if(response.data.service!=null) self.getServices(response.data.service.id)
                });
            }
        },
        getServices:function(id){
            var self=this
            var params={
                id: id
            }
            Utils.apiCall("get", "/kong/services",params)
            .then(function (response) {
                self.services=[response.data]
            });
        }
    },
    created:function() {
        this.loadData()
    },
}
</script>
