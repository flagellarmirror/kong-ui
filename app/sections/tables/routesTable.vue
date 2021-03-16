<template>
    <v-data-table
        :headers="headers"
        :items="routes"
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
                            @click="$emit('event',null)"
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
                            @click="sendEvent(index)"
                        >
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                    </td>
                    <td :key="i" v-else>{{ item[header['value']]}}</td>
                </template>
            </tr>
        </template>
    </v-data-table>
</template>

<script>
module.exports = {
    data:function(){
        return{
            routes:[],
            headers: [
                {
                    text: 'Actions',
                    value: null,
                    align: 'center',
                    sortable: false,
                    custom:{
                        type:"btn"
                    },
                    visible:true,
                },
                {
                    text: 'Route ID',
                    align: 'start',
                    value: 'id',
                    custom:{
                        type:"text"
                    },
                    visible:true,
                },
                {
                    text: 'Name',
                    value: 'name',
                    custom:{
                        type:"text"
                    },
                    visible:true,
                },
                {
                    text: 'Hosts',
                    value: 'hosts',
                    custom:{
                        type:"text"
                    },
                    visible:true,
                },
                {
                    text: 'Paths',
                    value: 'paths',
                    custom:{
                        type:"text"
                    },
                    visible:true,
                },
                {
                    text: 'Methods',
                    value: 'methods',
                    custom:{
                        type:"text"
                    },
                    visible:true,
                },
                {
                    text: 'Service ID',
                    value: 'service_id',
                    custom:{
                        type:"text"
                    },
                    visible:this.item.hide_link==undefined ? true : !this.item.hide_link
                },
            ],
        }
    },
    props:['item'],
    methods: {
        sendEvent:function(index){
            this.$emit('event',this.routes[index].id)
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
                    if(self.item.id==null){
                        tmp.push(response.data.data[i])
                    }else{
                        if(response.data.data[i].service.id==self.item.id){
                            tmp.push(response.data.data[i])
                        }
                    }
                    if(response.data.data[i].service.id!=null){
                        response.data.data[i].service_id=response.data.data[i].service.id
                    }
                }
                self.routes=tmp
            });
        }
    },
    created:function() {
        var tmp=[]
        for(var i=0;i<this.headers.length;i++){
            if(this.headers[i].visible) tmp.push(this.headers[i])
        }
        this.headers=tmp
        this.getRoutes();
    },
}
</script>
