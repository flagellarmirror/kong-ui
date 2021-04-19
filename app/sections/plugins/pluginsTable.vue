<template>
    <v-data-table
        :headers="headers"
        :items="plugins"
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
                        <v-btn
                            icon
                            @click="deleteRow(index)"
                        >
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                        <v-btn
                            icon
                            @click="sendEvent(index)"
                        >
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                    </td>
                    <td :key="i" v-else>{{item[header['value']]}}</td>
                </template>
            </tr>
        </template>
    </v-data-table>
</template>

<script>
module.exports = {
    data:function(){
        return{
            plugins:[],
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
                    text: 'Plugin ID',
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
                    text: 'Enabled',
                    value: 'enabled',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Protocols',
                    value: 'protocols',
                    custom:{
                        type:"text"
                    }
                }
            ],
        }
    },
    props:['item'],
    methods: {
        sendEvent:function(index){
            this.$emit('event',this.plugins[index].id)
        },
        deleteRow:function(index){
            var self=this
            var params={
                id: this.plugins[index].id
            }
            Utils.apiCall("delete", "/kong/plugins",params)
            .then(function (response) {
                if(response!=undefined){
                    Swal.fire({
                        type: 'success',
                        title: 'Plugin delete',
                        text: 'Plugin delete',
                    }).then(function(result) {
                        self.getPlugins()
                    })
                }
            });
        },
        getPlugins:function(){
            var self=this
            Utils.apiCall("get", "/kong/plugins")
            .then(function (response) {
                console.log(response)
                var tmp=[]
                for(var i=0;i<response.data.data.length;i++){
                    if(response.data.data[i].protocols!=null){
                        response.data.data[i].protocols=response.data.data[i].protocols.join(";")
                    }
                    tmp.push(response.data.data[i])
                }
                self.plugins=tmp
            });
        }
    },
    created:function() {
        this.getPlugins();
    },
}
</script>
