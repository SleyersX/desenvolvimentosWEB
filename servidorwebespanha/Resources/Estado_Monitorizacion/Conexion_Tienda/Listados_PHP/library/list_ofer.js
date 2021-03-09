	var last_id=0;
	$("#jqGrid").jqGrid({
			caption: Titulo,
			url: url_local+"?opcion_ofer=get_list_ofer&IP_Tienda="+IP_Tienda,
			mtype: "GET",
			datatype: "json",
			colModel:
			[
				{ index: 'Oferta', name: 'Oferta', label:'C.Oferta', key: true, width: 80 },
				{ index: 'Descripcion', name: 'Descripcion', label:'Descripcion', width: 200 },
				{ index: 'F_Inicio', name: 'F_Inicio', label:'Fecha Inicio', width: 100 },
				{ index: 'F_Fin', name: 'F_Fin', label:'Fecha Fin', width: 100 },
				{ index: 'Activo', name: 'Activo', label:'Activo', width: 70 },
			],
			sortname:"Oferta", gridview: true, viewrecords: true, page: 1,
			height:"100%", width:1500, autowidth:true,
			rowNum: 30,	scroll: 1, // set the scroll property to 1 to enable paging with scrollbar - virtual loading of records
			pager: "#jqGridPager",
			search: false, refresh: true, sortable: true, shrinkToFit: false,
			onSelectRow: function(id){
					if (id !== last_id) {
	      		   $('#info_adicional').html("<img src='/img/Loading-data.gif'/>").load(url_local+"?opcion_ofer=get_info&IP_Tienda="+IP_Tienda+"&Oferta="+id);
	      		   last_id=id;
	      		}
		   },
		});

		$('#jqGrid').jqGrid('filterToolbar',{ stringResult: true });
		$('#jqGrid').navGrid("#jqGridPager", { search: false, edit:false, add:false, del:false, refresh: true })
		.navSeparatorAdd("#jqGridPager",{});