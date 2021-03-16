<template>
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
                            @click="openServiceModal()"
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
                        <v-btn icon
                            @click="deleteRow(index)"
                        >
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
</template>

<script>
module.exports = {
    data:function() {
        return {
            services:[],
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
    methods: {
        openServiceModal:function(index){
            this.$emit("open-service-modal",index==undefined ? null : this.services[index].id)
        },
        deleteRow:function(index){
            var self=this

            var params={
                id: this.services[index].id
            }
            Utils.apiCall("delete", "/kong/services",params)
            .then(function (response) {
                if(response!=undefined){
                    Swal.fire({
                        type: 'success',
                        title: 'Service delete',
                        text: 'Service delete',
                    }).then(function(result) {
                        self.loadData()
                    })
                }
            });
        },
        loadData:function(){
            var self=this

            Utils.apiCall("get", "/kong/services")
            .then(function (response) {
                self.services=response.data.data
            });
        },
    },
    created:function() {
        this.loadData()
    },
}
</script>
