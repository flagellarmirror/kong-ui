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

                <!-- <v-flex xs12 pt-2 pl-2>
                    <v-divider></v-divider>
                </v-flex> -->

                <!-- <v-flex xs12 pt-2>
                    <v-text-field
                        outlined
                        dense
                        hide-details
                        label="Link"
                    ></v-text-field>
                </v-flex> -->
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

                <!-- Components -->
                <routes-table
                    v-if="type=='routes'"
                    :item="item"
                    @event="closeModal(false,'editRoute',$event)"
                ></routes-table>

                <plugins-table
                    v-if="type=='plugins'"
                    :item="item"
                ></plugins-table>

                <!-- <v-data-table
                    :headers="headers"
                    :items="routes"
                    :items-per-page="5"
                    class="elevation-1"
                    dense
                    v-if="type=='routes'"
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
                                        @click="closeModal(false,'editRoute')"
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
                                        @click="closeModal(false,'editRoute',index)"
                                    >
                                        <v-icon>mdi-pencil</v-icon>
                                    </v-btn>
                                </td>
                                <td :key="i" v-else>{{item[header['value']]}}</td>
                            </template>
                        </tr>
                    </template>
                </v-data-table> -->
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
            routes:[],
            form:{
                id:null,
                name:null,
                protocol:null,
                host:null,
                port:null,
                path:null,
            },
            type:"routes",
            type_items:[
                "routes",
                "plugins"
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
                    text: 'Route ID',
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
                    text: 'Hosts',
                    value: 'hosts',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Paths',
                    value: 'paths',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Methods',
                    value: 'methods',
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
                return 'Modify service: '+this.item.id
            }else{
                return "Create service"
            }

        },
    },
    methods: {
        closeModal:function(value,event,id){
            var tmp={
                event:event,
                refresh:value,
                data:{
                    parent_id:this.form.id,
                    id:id!=undefined ? id : null
                }
            }
            this.$emit('close-modal',tmp)
        },
        submit:function(){
            var self=this
            // this.form.port = parseInt(this.form.port)
            Utils.apiCall("post", "/kong/services",this.form)
            .then(function (response) {
                // PROBLEMA APICALL LA CHIAMATA FALLISCHE MA IL CODICE VIENE ESEGUITO
                if(response!=undefined){
                    Swal.fire({
                        type: 'success',
                        title: self.item==null ? 'New service create' : 'Service updated',
                        text: self.item==null ? 'New service create' : 'Service updated',
                    }).then(function(result) {
                        self.closeModal(true)
                    })
                }
            });
        },
        loadData:function(){
            var self=this

            if(this.item.id!=null){
                var params={
                    id:this.item.id
                }
                Utils.apiCall("get", "/kong/services",params)
                .then(function (response) {


                    self.form.id=response.data.id
                    self.form.name=response.data.name
                    self.form.protocol=response.data.protocol
                    self.form.host=response.data.host
                    self.form.port=response.data.port
                    self.form.path=response.data.path


                    // if(response.data.service!=null) self.getServices(response.data.service.id)
                });
            }

            // if(this.item!=null){
            //     this.form.id=this.item.id
            //     this.form.name=this.item.name
            //     this.form.protocol=this.item.protocol
            //     this.form.host=this.item.host
            //     this.form.port=this.item.port
            //     this.form.path=this.item.path
            //     this.getRoutes()
            // }
        },
        getRoutes:function(){
            var self=this
            Utils.apiCall("get", "/kong/routes")
            .then(function (response) {
                var tmp=[]
                for(var i=0;i<response.data.data.length;i++){
                    if(response.data.data[i].paths!=null){
                        response.data.data[i].paths=response.data.data[i].paths.join(";")
                    }
                    if(response.data.data[i].methods!=null){
                        response.data.data[i].methods=response.data.data[i].methods.join(";")
                    }
                    if(response.data.data[i].service.id==self.item.id){
                        tmp.push(response.data.data[i])
                    }
                }
                self.routes=tmp
            });
        }
    },
    created:function() {
        this.loadData()
    },
    components:{
        'routes-table': httpVueLoader('./../tables/routesTable.vue' + '?v=' + new Date().getTime()),
        'plugins-table': httpVueLoader('./../tables/pluginsTable.vue' + '?v=' + new Date().getTime())
    }
}
</script>
