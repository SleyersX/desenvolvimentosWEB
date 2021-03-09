	var last_id=0;
	$("#jqGrid").jqGrid({
			caption: Titulo,
			url: url_local+"?opcion_arti=get_list_arti&IP_Tienda="+IP_Tienda,
			mtype: "GET",
			datatype: "json",
			colModel:
			[
				{ index: 'ITEM_ID', 		name: 'ITEM_ID',    	label:'ITEM', key: true, width: 100 },
				{ index: 'DESCRIPTION', name: 'DESCRIPTION', label:'Descripcion', width: 280 },
			],
			sortname:"ITEM_ID", gridview: true, viewrecords: true, page: 1,
			height:"100%", width:1500, autowidth:true,
			rowNum: 30,	scroll: 1, // set the scroll property to 1 to enable paging with scrollbar - virtual loading of records
			pager: "#jqGridPager",
			search: false, refresh: true, sortable: true, shrinkToFit: false,
			onSelectRow: function(id){
					if (id !== last_id) {
	      		   $('#info_articulo').html("<img src='/img/Loading-data.gif'/>").load(url_local+"?opcion_arti=get_info&IP_Tienda="+IP_Tienda+"&item_id="+id);
	      		   last_id=id;
	      		}
		   },
		});

		$('#jqGrid').jqGrid('filterToolbar',{ stringResult: true });
		$('#jqGrid').navGrid("#jqGridPager", { search: false, edit:false, add:false, del:false, refresh: true })
		.navSeparatorAdd("#jqGridPager",{});