var billsJs = {
        dashboard: {
            getGroupListChartData: function(element) {
                if (element != undefined) {
                    var groupId = element.value;
                    var id = element.id;
                } else {
                    var groupId = 'groupId';
                    var id = '';
                }

                if (groupId != '' && groupId != undefined) {
                    jQuery.ajax({
                        url: '?option=com_bills&task=dashboard.getGroupListChartData',
                        dataType: 'json',
                        type: 'POST',
                        data: {
                            'groupId': groupId,
                            'id': id
                        },
                        success: function(response) {
                            console.log(response);
                            if (response != 'undefined' && !jQuery.isEmptyObject(response) ) {
                                if (id == 'search-filter-group-list1') {
                                    myPieChart1.data.datasets[0].data = response.data;
                                    myPieChart1.data.labels = response.labels;
                                    myPieChart1.backgroundColor = response.colors;
                                    myPieChart1.hoverBackgroundColor = response.colors;
                                    myPieChart1.update();
                                } else if (id == 'search-filter-group-list2') {
                                    myPieChart2.data.datasets[0].data = response.data;
                                    myPieChart2.data.labels = response.labels;
                                    myPieChart2.backgroundColor = response.colors;
                                    myPieChart2.hoverBackgroundColor = response.colors;
                                    myPieChart2.update();
                                } else {
                                    myPieChart3.data.datasets[0].data = response.data;
                                    myPieChart3.data.labels = response.labels;
                                    myPieChart3.backgroundColor = response.colors;
                                    myPieChart3.hoverBackgroundColor = response.colors;
                                    myPieChart3.update();
                                }
                            }
                        }
                    });
                }
            },
            dashboardInit: function() {
                jQuery(document).ready(function() {
                    billsJs.dashboard.getGroupListChartData(jQuery('#search-filter-group-list1'));
                    billsJs.dashboard.getGroupListChartData(jQuery('#search-filter-group-list2'));
                    billsJs.dashboard.getGroupListChartData();
                });
            }
        },
        bill: {
            getUsersFromGroup: function(groupId) {
                jQuery.ajax({
                        url: '?option=com_bills&task=bill.getUsersFromGroup',
                        dataType: 'json',
                        type: 'POST',
                        data: {
                            'groupId': groupId
                        },
                        success: function(response) {
                            console.log(response);
                            if (response.length > 0) {
                            	jQuery('#jform_for_users option').remove();
                            	jQuery('#jform_for_users').append(jQuery('<option>', {
                                    value: "-1",
                                    text: "-- Select users --",
                                    disabled : true
                                }));

                                jQuery.each(response, function(i, item) {
                                        jQuery('#jform_for_users').append(jQuery('<option>', {
                                            value: item.value,
                                            text: item.text
                                        }));
                            	});
                            	jQuery('#jform_for_users').trigger("liszt:updated");
                    		}
                		}
            		});
            }
        }
    }