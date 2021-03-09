	var last_id=0;
	$("#jqGrid").jqGrid({
			caption: Titulo,
			url: url_local+"?opcion_arti=get_list_arti&IP_Tienda="+IP_Tienda,
			mtype: "GET",
			datatype: "json",
			colModel:
			[
				{ index: 'ITEM', 		name: 'ITEM',    	label:'ITEM', key: true, width: 90 },
				{ index: 'DESCRIPCION', name: 'DESCRIPCION', label:'Descripcion', width: 180 },
				{ index: 'Tipo_PVP', name: 'Tipo_PVP', label:'Tipo PVP', width: 100 },
				{ index: 'F_INICIO', name: 'F_INICIO', label:'Fecha Inicio', width: 100 },
				{ index: 'F_FIN', name: 'F_FIN', label:'Fecha Fin', width: 100 },
				{ index: 'PVP', name: 'PVP', label:'Precio', width: 100 },
			],
			sortname:"ITEM", gridview: true, viewrecords: true, page: 1,
			height:"100%", width:"100%", autowidth:true,
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