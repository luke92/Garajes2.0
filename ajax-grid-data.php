<?php

 include "conn.php";

/* Database connection end */


// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;


$columns = array( 
// datatable column index  => database column name
	0 => 'REF',
	1 => 'IDV',
    2 => 'IDCON', 
	3 => 'DIRECCION',
	4 => 'LOCALIDAD',
    5 => 'PROVINCIA',
    6 => 'RS',  
	7 => 'EQUIPO',
	8 => 'SERIAL',
	9 => 'LP',
	10 => 'LS',
	11 => 'WC',
	12 => 'WP',
	13 => 'T0',
	14 => 'T1',
	15 => 'T2',
	16 => 'T3',
	17 => 'Loopback',
	18 => 'IP 3G',
	19 => 'SIM',
	20 => 'P3G',
	
);

// getting total number records without any search
$sql = "SELECT REF, IDV, IDCON, DIRECCION, LOCALIDAD, PROVINCIA , RS,  EQUIPO,  SERIAL,  LP,  LS,  WC,  WP,  T0,  T1,  T2,  T3,  Loopback, 'IP 3G',  SIM,  P3G  ";
$sql.=" FROM garajes_amba_final";
$query=mysqli_query($conn, $sql) or die("ajax-grid-data.php: get InventoryItems");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if( !empty($requestData['search']['value']) ) {
	// if there is a search parameter
	$sql = "SELECT REF, IDV, IDCON, DIRECCION, LOCALIDAD, PROVINCIA , RS,  EQUIPO,  SERIAL,  LP,  LS,  WC,  WP,  T0,  T1,  T2,  T3,  Loopback, 'IP 3G',  SIM,  P3G  ";
	$sql.=" FROM garajes_amba_final";
	$sql.=" WHERE REF LIKE '".$requestData['search']['value']."%' ";    // $requestData['search']['value'] contains search paramete'
	$sql.=" OR 'IDV' LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR 'IDCON' LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR 'DIRECCION' LIKE '".$requestData['search']['value']."%' ";
    $sql.=" OR 'LOCALIDAD' LIKE '".$requestData['search']['value']."%' ";
    $sql.=" OR 'PROVINCIA' LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR 'RS' LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR 'EQUIPO' LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR 'SERIAL' LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR 'LP' LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR 'WC' LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR 'WP' LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR 'Loopback' LIKE '".$requestData['search']['value']."%' ";
	$query=mysqli_query($conn, $sql) or die("ajax-grid-data.php: get PO");
	$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result without limit in the query 

	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   "; // $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc , $requestData['start'] contains start row number ,$requestData['length'] contains limit length.
	$query=mysqli_query($conn, $sql) or die("ajax-grid-data.php: get PO"); // again run query with limit
	
} else {	

	$sql = "SELECT REF, IDV, IDCON, DIRECCION, LOCALIDAD, PROVINCIA , RS,  EQUIPO,  SERIAL,  LP,  LS,  WC,  WP,  T0,  T1,  T2,  T3,  Loopback, 'IP 3G',  SIM,  P3G  ";
	$sql.=" FROM garajes_amba_final";
	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	$query=mysqli_query($conn, $sql) or die("ajax-grid-data.php: get PO");
	
}

$data = array();
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 

	$nestedData[] = $row['REF'];
	$nestedData[] = $row['IDV'];
    $nestedData[] = $row["IDCON"];
	$nestedData[] = $row["DIRECCION"];
	$nestedData[] = $row["LOCALIDAD"];
    $nestedData[] = $row["PROVINCIA"];
	$nestedData[] = $row["RS"];
	$nestedData[] = $row["EQUIPO"];
	$nestedData[] = $row["SERIAL"];
	$nestedData[] = $row["LP"];
	$nestedData[] = $row["LS"];
	$nestedData[] = $row["WC"];
	$nestedData[] = $row["WP"];
	$nestedData[] = $row["T0"];
	$nestedData[] = $row["T1"];
	$nestedData[] = $row["T2"];
	$nestedData[] = $row["T3"];
	$nestedData[] = $row["Loopback"];
	$nestedData[] = $row["IP 3G"];
	$nestedData[] = $row["P3G"];
	$nestedData[] = $row["SIM"];
    //$nestedData[] = date("d/m/Y", strtotime($row["registrado"]));
    $nestedData[] = '<td><center>
                     <a href="editar.php?id='.$row['REF'].'"  data-toggle="tooltip" title="Editar datos" class="btn btn-sm btn-info"> <i class="menu-icon icon-pencil"></i> </a>
                     <a href="index.php?action=delete&id='.$row['REF'].'"  data-toggle="tooltip" title="Eliminar" class="btn btn-sm btn-danger"> <i class="menu-icon icon-trash"></i> </a>
				     
					 <a href="profile.php?id='.$row['REF'].'"  data-toggle="tooltip" title="Ver" class="btn btn-sm btn-success"> <i class="menu-icon icon-eye-open"></i> </a>
					 				     </center></td>';		
	
	$data[] = $nestedData;
    
}



$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
			);

echo json_encode($json_data);  // send data as json format

?>
