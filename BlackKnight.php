<?php 

class BlackKnight{

	private $_address_obj;

	private $_key;

	private $_base_url;

	private $_matches;

	private $loan;

	public function __construct($loan, $role)
	{
		// $this->_base_url = "https://rc.api.sitexdata.com/sitexapi/sitexapi.asmx/"; //release candidate url
		// $this->_base_url = "https://api.sitexdata.com/sitexapi/sitexapi.asmx/"; //live url
		$this->_base_url = "https://sitexproapi.uat.bkitest.com/SiteXProService.svc";
		$this->wsdl = "https://sitexproapi.uat.bkitest.com/SiteXProService.svc?singlewsdl";
		$this->_key = '';
		$this->_matches = false;
		$this->loan = $loan;
		$this->role = $role;

	}

	protected function getKey()
	{
		return $this->_key;
	}

	protected function getURL($action,$query)
	{
		return $this->_base_url.$action.'?'.$query;
	}

	protected function getbaseURL()
	{
		return $this->_base_url;
	}

	protected function getWSDL()
	{
		return $this->wsdl;
	}

	public function organizeMagChart($report, $loan_type)
	{	
		
		if(is_object($report)): 
			 //DECLARATIONS: 
			//LEGAL INFO
			$reportLEGI = $report->LegalInfo;
			// LOCATIONINFO
			$reportLI = $report->LocationInfo;
			//OWNERINFO
			$reportOI = $report->OwnerInfo;
			// PROPERTYINFO
			$reportPI = $report->PropertyInfo;
			//ASSESSMENT TAX
			$reportAT = $report->AssessmentTax;
			//COMPARABLES:
			$reportC = $report->Comparables;
			//CURRENTOWNERNAMES
			$reportCON = $report->CurrentOwnerNames;
			//FLOODREPORT
			$reportFR = $report->FloodReport;
			//TRANSACTIONS 
			$reportT =  $report->Transactions;
			//TRANSACTIONS ::Mortgage
			$reportTMortgage =  $reportT->Mortgage;
			//TRANSACTIONS ::Transfer 
			$reportTTransfer =  $reportT->Transfer;
			
			$pricesqft = 0;


			//LEGAL INFO
				// Brief Legal Description for commercial
				if( !empty($reportLEGI->LegalBriefDescription) ){
					$data['facts']['Legal Description'] = (string)$reportLEGI->LegalBriefDescription;
				}
			
			// LOCATIONINFO STARTS

				/* Header */
				// Location
				$data['header']['Location']					= (string)$reportLI->SiteAddress;


				// APN
				$data['facts']['APN'] 						= "APN Not Found";
				if(!empty((string)$reportLI->APN))
					$data['facts']['APN']					= (string)$reportLI->APN;
				
				//County
				$data['address']['County'] 				= ucwords(strtolower((string)$reportLI->County));

				/* Property Address Parsed */
				if( !empty((string)$reportLI->SiteAddress) ){
					$data['address']['Address'] 			= ucwords(strtolower((string)$reportLI->SiteAddress));
				}else{
					$data['address']['Address'] 			= "Exact Match Unavailable";
				}

				//City
				$data['address']['City'] 					= ucwords(strtolower((string)$reportLI->SiteCity));

				//State
				$data['address']['State'] 					= (string)$reportLI->SiteState;

				// Site Unit 
				if( !empty((string)$reportLI->SiteUnit) ){
					$data['facts']['Site Unit']				= (string)$reportLI->SiteUnit;
				}

				// Site Unit Type
				if( !empty((string)$reportLI->SiteUnitType) ){
					$data['facts']['Site Unit Type']				= (string)$reportLI->SiteUnitType;
				}

				//Zip
				$data['address']['Zip'] 					= (string)$reportLI->SiteZip;

				//ZIP4
				$data['extra']['Zip4']						=(string)$reportLI->SiteZip4;

			// LOCATIONINFO ENDS
			//OWNERINFO STARTS
				
				if( !empty((string)$reportOI->OwnerNames) ){
					$data['extra']['Owner Name'] 		= (string)$reportOI->OwnerNames;
				}
				if( !empty((string)$reportOI->MailAddress) ){
					$data['extra']['Owner Address'] = (string)$reportOI->MailAddress;
					$data['extra']['Owner Full Address'] = (string)$reportOI->MailAddress;
				}
				if( !empty((string)$reportOI->MailCity) ){
					$data['extra']['Owner City'] 		= (string)$reportOI->MailCity;
					$data['extra']['Owner Full Address'] .= ', '.(string)$reportOI->MailCity;
				}
				if( !empty((string)$reportOI->MailState) ){
					$data['extra']['Owner State'] 	= (string)$reportOI->MailState;
					$data['extra']['Owner Full Address'] .= ', '.(string)$report->MailState;
				}
				if( !empty((string)$reportOI->MailZip) ){
					$data['extra']['Owner Zip'] 		= (string)$reportOI->MailZip;
					$data['extra']['Owner Full Address'] .= ' '.(string)$reportOI->MailZip;
				}

			//OWNERINFO ENDS

			//  PROPERTYINFO STARTS
			//  
				// A/C
				$data['facts']['A/C Type']			= '';	
				if( !empty((string)$reportPI->AirConditioning) ){
					$data['facts']['A/C Type']			= (string)$reportPI->AirConditioning;
				}

				// Basement Type/Area
				$data['facts']['Basement Type / Area'] 	= '';
				if( !empty((string)$reportPI->Basement) ){
					$data['facts']['Basement Type / Area']	= (string)$reportPI->Basement;
				}

				//Building Area
				$data['facts']['Building Area (Sqft)'] = '';
				if( !empty((string)$reportPI->BuildingArea) ){
					$data['facts']['Building Area (Sqft)']				= (string)$reportPI->BuildingArea;
				}

				//Building Style
				$data['facts']['Style']	 = '';
				if( !empty((string)$reportPI->BuildingStyle) ){
					$data['facts']['Style']				= (string)$reportPI->BuildingStyle;
				}

				// Construction Type
				$data['facts']['Construction Type'] = '';
				if( !empty((string)$reportPI->ConstructionType) ){
					$data['facts']['Construction Type'] 	= (string)$reportPI->ConstructionType;
				}

				// County Land Use Code
				$data['facts']['Construction Land Use Code'] = '';
				if( !empty((string)$reportPI->CountyLandUseCode) ){
					$data['facts']['Construction Land Use Code'] 	= (string)$reportPI->CountyLandUseCode;
				}

				// County Land Use Description
				$data['facts']['County Land Use Description'] = '';
				if( !empty((string)$reportPI->CountyLandUseDescription) ){
					$data['facts']['County Land Use Description'] 	= (string)$reportPI->CountyLandUseDescription;
				}
				
				// Elevator
				if( !empty((string)$reportPI->Elevator) ){
					
					if((string)$reportPI->Elevator == 'Y'){
						$data['facts']['Elevator']			= 'Yes';
					}else{
						$data['facts']['Elevator']			= '';
					}
				}

				// Exterior Walls
				if( !empty((string)$reportPI->ExteriorWalls) ){
					$data['facts']['Exterior Walls'] 			= (string)$reportPI->ExteriorWalls;
				}

				// Garage/No. of Cars
				$data['facts']['Garage / No. of Cars']	= '';
				if( (string)$reportPI->GarageNumCars){
					$data['facts']['Garage / No. of Cars']	= 'Garage / '.(string)$reportPI->GarageNumCars;
				}

				// Heat Type
				$data['facts']['Heat Type']			= '';
				if( !empty((string)$reportPI->Heating) ){
					$data['facts']['Heat Type']			= (string)$reportPI->Heating;
				}

				// Lot Size (SF/AC)
				$data['facts']['Lot Size']		= '';
				if( !empty((string)$reportPI->LotSize)){
					$data['facts']['Lot Size']		= APP_Util::niceNumber((string)$reportPI->LotSize).' '.$reportPI->LotSizeUnits;
				}	

				// No. of Buildings
				$data['facts']['No. of Buildings']			= '';
				if( !empty((string)$reportPI->NumBuildings) && (string)$reportPI->NumBuildings != "0" ){
					$data['facts']['No. of Buildings']		= (string)$reportPI->NumBuildings;
				}

				// Year Built
				$data['facts']['Year Built']					= '';
				if( !empty((string)$reportPI->YearBuilt) ){
					$data['facts']['Year Built']					= (string)$reportPI->YearBuilt;
				}	
					
			//  PROPERTYINFO ENDS 

			// Last Valuation and date of valuation - Assessment Year
					if(!empty($reportAT->AssessedTotalValue) ){
						$data['facts']['Current Assessed Value'] = '$' . APP_Util::niceNumber((string)$reportAT->AssessedTotalValue) . ' <span style="font-weight:100;"> (' . date( 'Y',strtotime((string)$reportAT->AssessmentYear) ) . ') </span>';
					}

					// Value
					$data['header']['Value']			= (string)$reportAT->AssessedTotalValue;

					

			// 	ASSESSMENT TAX ENDS
			//  COMPARABLES STARTS
				// The history : Comparables 
					$comparables = $reportC->Comparable;
					$sum_pricesqft = 0;
					$total_count = 0;
					$sqft_months_for_comparison = "-6 months";
					$six_months_prior = date('Y-m-d', strtotime($sqft_months_for_comparison));
					$sqft_to_acres_divisor = 43560;
					$min_comparable = 0;
					$max_comparable = 0;
					$total_comp_psqft = 0;
					$count_comp_psqft = 0;

					for( $i = 0; $i <= 50; $i++ ){
						if( !empty($comparables->$i) ){

							// $data['history']['comparables'][$i]['ID']				= $i + 1;

							//$data['history']['comparables'][$i]['Address']			= ucwords(strtolower((string)$comparables->$i->SiteAddress));
																								
							$data['history']['comparables'][$i]['Sale Date']		= date('m.d.Y',strtotime((string)$comparables->$i->RecordingDate));

							$data['history']['comparables'][$i]['Price']			= '$'.APP_Util::niceNumber((string)$comparables->$i->SalePrice);

							$data['history']['comparables'][$i]['Sqft']				= APP_Util::niceNumber((string)$comparables->$i->BuildingArea);

							$data['history']['comparables'][$i]['$/Sqft']			= '$'.APP_Util::niceNumber((string)$comparables->$i->PricePerSQFT);

							if(($min_comparable == 0 && (int)$comparables->$i->PricePerSQFT) || ((int)$comparables->$i->PricePerSQFT < $min_comparable)){
								$min_comparable = (int)$comparables->$i->PricePerSQFT;
							}

							if(($max_comparable == 0 && (int)$comparables->$i->PricePerSQFT > 0) || ((int)$comparables->$i->PricePerSQFT > $max_comparable)){
								$max_comparable = (int)$comparables->$i->PricePerSQFT;
							}

							if((int)$comparables->$i->PricePerSQFT > 0){
								$total_comp_psqft = $total_comp_psqft + (int)$comparables->$i->PricePerSQFT;
								$count_comp_psqft ++;
							}

							if((int)$comparables->$i->PricePerSQFT > 0 && date('Y-m-d',strtotime((string)$comparables->$i->RecordingDate)) > $six_months_prior){
								$sum_pricesqft += (int)$comparables->$i->PricePerSQFT;
								$total_count++;
							}

							

							$data['history']['comparables'][$i]['Lot']			=  
								round( ((int)$comparables->$i->LotSize / $sqft_to_acres_divisor), 2 ) ;

							if($loan_type == 'home'){

								$data['history']['comparables'][$i]['Bedrooms']			= (string)$comparables->$i->Bedrooms;

								$data['history']['comparables'][$i]['Baths']			= (string)$comparables->$i->Baths;
							}

							$data['history']['comparables'][$i]['Year Built']		= (string)$comparables->$i->YearBuilt;

							$data['history']['comparables'][$i]['Proximity']		= (string)$comparables->$i->Proximity;

							// $data['history']['comparables'][$i]['Rooms']			= (string)$comparables->$i->TotalRooms;

							$data['history']['comparables'][$i]['Address']	= (string)$comparables->$i->SiteAddress;

							 $data['history']['comparables'][$i]['City']				= (string)$comparables->$i->SiteCity.', '.(string)$comparables->$i->SiteState;

							// $data['history']['comparables'][$i]['State']			= (string)$comparables->$i->SiteState;

							 $data['history']['comparables'][$i]['Zip']				= (string)$comparables->$i->SiteZip;


							// $data['history']['comparables'][$i]['Stories']			= (string)$comparables->$i->NumStories;

							// $data['history']['comparables'][$i]['Land Use']			= (string)$comparables->$i->UseCodeDescription;
						}
				}

			$data['history']['graph']['min_comparable'] = $min_comparable;
			$data['history']['graph']['max_comparable'] = $max_comparable;
			$data['history']['graph']['total_comp_psqft'] = $total_comp_psqft;
			$data['history']['graph']['avg_comp_psqft'] = (int)($total_comp_psqft / $count_comp_psqft);

			//COMPARABLES ENDS
			//CURRENTOWNERNAMES STARTS
				// Current Owner
				if( !empty((string)$reportCON->Names) ){
					$data['facts']['Current Owner'] 	= (string)$reportCON->Names;
				}
			//CURRENTOWNERNAMES ENDS
			//TRANSACTION STARTS
				//TRANSACTION INFO: TRANSFER
				
					$first = 0;
					if( $reportTTransfer->$first->ContractDate){
					$data['facts']['Latest Transfer'] = date( 'm.d.Y',strtotime((string)$reportTTransfer->$first->ContractDate) ) . ' <span style="font-weight:100;"> (' . (string)$reportTTransfer->$first->RecorderDocumentNumber . ')</span>';
					} 

					// The History : Price History 
					$transfer = $reportTTransfer;

					for( $i = 0; $i <= 9; $i++ )
					{	
						if( !empty($transfer->$i) )
						{	
							// $data['history']['transactions'][$i]['ID']						= $i + 1;

							$data['history']['transactions'][$i]['Date']			= date( 'm.d.Y',strtotime((string)$transfer->$i->ContractDate) );

							if( !empty((string)$transfer->$i->RecorderDocumentNumber) ){	
								$data['history']['transactions'][$i]['Document']		= (string)$transfer->$i->RecorderDocumentNumber;}
							else{
								$data['history']['transactions'][$i]['Document']		= '';
							}

							// $data['history']['transactions'][$i]['Document Description']	= (string)$transfer->$i->DocumentType;

							if( !empty($transfer->$i->Deed->SalesPrice) ){
								$data['history']['transactions'][$i]['Sales Price']	= '$'.APP_Util::niceNumber((string)$transfer->$i->Deed->SalesPrice);
							}elseif( !empty($transfer->$i->Loan1Amount) ){
								$data['history']['transactions'][$i]['Sales Price']	= '$'.APP_Util::niceNumber((string)$transfer->$i->Loan1Amount);
							}else{
								$data['history']['transactions'][$i]['Sales Price']	= '';
							}
							
							$data['history']['transactions'][$i]['Buyer']		= (string)$transfer->$i->Deed->BuyerInfo->Buyers->Buyer->FirstAndMiddleName.' '.(string)$transfer->$i->Deed->BuyerInfo->Buyers->Buyer->LastOrCorporateName;

							if( !empty((string)$transfer->$i->Deed->SellerInfo->Sellers->Seller->FirstAndMiddleName) ){
								$data['history']['transactions'][$i]['Seller']				= (string)$transfer->$i->Deed->SellerInfo->Sellers->Seller->FirstAndMiddleName.' '.(string)$transfer->$i->Deed->SellerInfo->Sellers->Seller->LastOrCorporateName;
							}elseif( !empty((string)$transfer->$i->LenderName) ){
								$data['history']['transactions'][$i]['Seller']				= (string)$transfer->$i->LenderName;
							}else{
								$data['history']['transactions'][$i]['Seller']				= '';
							}

						}

					}

				//TRANSACTION INFO: MORTGAGE
					//The History : Open Loans
					for( $i = 0; $i <= 9; $i++ )
					{
						// Most recent mortage data, if applicable.
						if( !empty($reportTMortgage->$i->Mortgage))
						{
							
							$data['history']['mortgage'][$i]['RecordingDate']				= date( 'm/d/Y',strtotime((string)$reportTMortgage->$i->RecordingDate) );

							if( !empty((string)$reportTMortgage->$i->Mortgage->LoanTypeCodeDesc) ){
								$data['history']['mortgage'][$i]['Loan Type']			= (string)$reportTMortgage->$i->Mortgage->LoanTypeCodeDesc;
							}else{
								$data['history']['mortgage'][$i]['Loan Type']			= '';
							}

							if( !empty($reportTMortgage->$i->Mortgage->LoanAmount) ){
								$data['history']['mortgage'][$i]['Loan Amount']				= '$' . APP_Util::niceNumber((string)$reportTMortgage->$i->Mortgage->LoanAmount);
							}else{
								$data['history']['mortgage'][$i]['Loan Amount']				= '';
							}

							$data['history']['mortgage'][$i]['Document Description']		= (string)$reportTMortgage->$i->DocumentType;


							$data['history']['mortgage'][$i]['Document Number']				= (string)$reportTMortgage->$i->RecorderDocumentNumber;

							$data['history']['mortgage'][$i]['Borrower']					= (string)$reportTMortgage->$i->Mortgage->BorrowerInfo->Borrowers->Borrower->FirstAndMiddleName.' '.(string)$reportTMortgage->$i->Mortgage->BorrowerInfo->Borrowers->Borrower->LastOrCorporateName;

							$data['history']['mortgage'][$i]['Lender']						= (string)$reportTMortgage->$i->Mortgage->LenderName;
						}
					}


			//TRANSACTION ENDS
			
				if( $loan_type == 'home')
				{
					
					//  PROPERTYINFO ONLY FOR HOME STARTS
					// Baths
					$data['facts']['Baths / Partial'] 	= '';
					if( !empty((string)$reportPI->Baths))
						$data['facts']['Baths / Partial'] 	= (string)$reportPI->Baths;
					if(!empty((string)$reportPI->PartialBaths))
						$data['facts']['Baths / Partial'] 	= (string)$reportPI->Baths.'/'. $reportPI->PartialBaths;
						
					// Bedrooms
					$data['facts']['Bedrooms'] = '';
					if(!empty((string)$reportPI->Bedrooms))
						$data['facts']['Bedrooms']					= (string)$reportPI->Bedrooms;

					//Building Condition
					$data['facts']['Building Condition'] = '';
					if( !empty((string)$reportPI->BuildingCondition) ){
						$data['facts']['Building Condition']				= (string)$reportPI->BuildingCondition;
					}

					//Effective Year BUilt
					$data['facts']['Effective Year Built'] = '';
					if( !empty((string)$reportPI->EffectiveYearBuilt) ){
						$data['facts']['Effective Year Built']				= (string)$reportPI->EffectiveYearBuilt;
					}

					// Fireplace
					$data['facts']['Fireplace']			= '';
					if( !empty((string)$reportPI->FirePlace) ){
						$data['facts']['Fireplace']			= (string)$reportPI->Fireplace;
					}
					
					// FloorCover
					$data['facts']['Floorcover']			= '';
					if( !empty((string)$reportPI->FloorCover) ){
						$data['facts']['Floorcover']			= (string)$reportPI->FloorCover;
					}

					// Interior Walls
					if( !empty((string)$reportPI->InteriorWalls) ){
						$data['facts']['Interior Walls'] 			= (string)$reportPI->InteriorWalls;
					}

					// Pool
					$data['facts']['Pool']				= '';
					if( !empty((string)$reportPI->Pool) ){
						$data['facts']['Pool']				= (string)$reportPI->Pool;
					}


					//  PROPERTYINFO ONLY FOR HOME ENDS

					
					//  ASSESSMENT TAX ONLY FOR HOME STARTS
						// Price ($/Sqft)
						$data['facts']['Price ($/Sqft)'] = '';
						if(!empty($reportAT->AssessedTotalValue)){
							$pricesqft = round((int)$reportAT->AssessedTotalValue / (int)$data['facts']['Building Area (Sqft)']);
							$data['facts']['Price ($/Sqft)'] = '$' . APP_Util::niceNumber(round((int)$reportAT->AssessedTotalValue / (int)$data['facts']['Building Area (Sqft)']));
						}
					//  ASSESSMENT TAX ONLY FOR HOME ENDS
					
				}
				else if($loan_type == "business")
				{
					//  PROPERTYINFO ONLY FOR COMMERCIAL STARTS
					
						// Foundation Type
						$data['facts']['Foundation Type'] = '';
						if( !empty((string)$reportPI->Foundation) ){
							$data['facts']['Foundation Type']			= (string)$reportPI->Foundation;
						}

						// Garage Type
						$data['facts']['Garage Type']	= '';
						if(!empty($reportPI->GarageType)){
							$data['facts']['Garage Type']	= (string)$reportPI->GarageType;
						}

						// Stories/Floors
						$data['facts']['Stories / Floors']		= '';
						if( !empty((string)$reportPI->NumStories) ){	
							if( (string)$reportPI->NumStories == '1' ){
								$data['facts']['Stories / Floors']		= (string)$reportPI->NumStories.' Storied';
							}else{
								$data['facts']['Stories / Floors']		= (string)$reportPI->NumStories.' Stories';
							}
						}

						// No. of Units
						$data['facts']['No. of Units']			= '';
						if( !empty((string)$reportPI->NumUnits) && (string)$reportPI->NumUnits != "0" ){
							$data['facts']['No. of Units']			= (string)$reportPI->NumUnits;
						}


						//Roof Material
						$data['facts']['Roof Material']						= '';
						if( !empty((string)$reportPI->RoofMaterial)){
							$data['facts']['Roof Material']						= (string)$reportPI->RoofMaterial;
						}

						//Roof Type
						$data['facts']['Roof Type']						= '';
						if( !empty((string)$reportPI->RoofType) ){
							$data['facts']['Roof Type']						= (string)$reportPI->RoofType;
						}

						// Total Rooms
						if( !empty((string)$reportPI->TotalRooms) && (string)$reportPI->TotalRooms != "0" ){
							$data['facts']['Total Rooms'] 				= (string)$reportPI->TotalRooms;
						}
				
						// Zoning 
						if( !empty((string)$reportPI->Zoning)){
							$data['facts']['Zoning']					= (string)$reportPI->Zoning;
						}
				


					//  PROPERTYINFO ONLY FOR COMMERCIAL ENDS

					// FLOODREPORT ONLY FOR COMMERCIAL STARTS
						$data['facts']['Flood Zone'] = (string)$reportFR->FloodZone;
						$data['facts']['SFHA'] = (string)$reportFR->SFHA;
						$data['facts']['Flood Zone Description'] = (string)$reportFR->FloodZoneDescription;

					// FLOODREPORT ONLY FOR COMMERCIAL ENDS
					
					//  ASSESSMENT TAX ONLY FOR HOME STARTS
						// Price ($/SF)
						if(!empty($reportAT->AssessedTotalValue) && !empty($data['facts']['Building Area (Sqft)'])){
							$data['facts']['Price ($/Sqft)'] = '$' . APP_Util::niceNumber(round((int)$reportAT->AssessedTotalValue / (int)$data['facts']['Building Area (Sqft)']));
							$pricesqft = round((int)$report->AssessedTotalValue / (int)$data['facts']['Building Area (Sqft)']);
						}elseif( !empty($reportAT->AssessedTotalValue) && !empty($reportPI->LotSize) ){
							$data['facts']['Price ($/Sqft)'] = '$' . APP_Util::niceNumber(round(((int)$reportAT->AssessedTotalValue / (int)$reportPI->LotSize)));
							$pricesqft = round(((int)$report->AssessedTotalValue / (int)$reportPI->LotSize));
						}else{
							$data['facts']['Price ($/Sqft)'] = '';
						}

					//  ASSESSMENT TAX ONLY FOR HOME ENDS
			}
			
			if($sum_pricesqft > 0 && $total_count > 0){
				$avg_pricesqft = APP_Util::niceNumber($sum_pricesqft / $total_count);
				$data['header']['Average Price ($/Sqft)'] =  '$'.$avg_pricesqft;
				if($data['facts']['Price ($/Sqft)'] == ''){
					$data['header']['comparisoncolor'] = 'black';
				}elseif($avg_pricesqft > $pricesqft){
					$data['header']['comparisoncolor'] = 'red';
					$data['header']['Comparison Message ($/Sqft)'] = "Your property's price per sqft is below the market average.";
				}else{
					$data['header']['comparisoncolor'] = 'green';
					$data['header']['Comparison Message ($/Sqft)'] = "Your property's price per sqft is above the market average.";
				}
			}else{
				$data['header']['comparisoncolor'] = 'black';
				$data['header']['Comparison Message ($/Sqft)'] = '';
			}

			
			
			return $data;
		endif;
	}

	public function getPropFaqs()
	{
		$report			= $this->queryAddress();
		
			$magchartdata		= $this->organizeMagChart($report, $this->loan->type());

			

		if($magchartdata['header']['Location'])
			return $magchartdata;
		else
			return $report;
	}




	public function queryAddress($lender = 'false')
	{
	
		$address = explode(',',$this->loan->address());
		// $address = explode(',','3234 Marysville Blvd, Sacramento, CA 95815'); 

		if(empty($address[0])){
			//If you're here, you may need to rework how the address is parsed. 
			//This is rush order so there is minimal error coverage.
			echo '<div id="Container" style="padding-top:10px; padding-bottom:20px" class="portlet">
   						<div class="portlet-body magchat-container" style ="padding-bottom: 40px;">
							<div class="white-cover">
								<img style="max-height:100px;"  id="SourceLogo" src="/skin/img/blackknight.png"/>
								<h4>Unfortunately, the address is either incorrect or is incomplete. Blackknight cannot provide information about it. </h4>
							</div>
						</div>
					</div>';
				exit(0);
		}

		$address = $address[0];
		$LastLine = $this->loan->city().', '.$this->loan->state().', '.$this->loan->zip();
		$OwnerName = '';
		$ClientReference = $this->loan->id();

		//Choose report depending on the loan type.
		if(in_array($this->role, array('lender','admin','brandadmin')))
		{
			if($this->loan->type()=='home'):
				$ReportType = '100001';
			else:
				$ReportType = '100002';
			endif;
		}
		else //Borrower has propfaqs and is only home loans
		 {
			// $ReportType = '100001';
			if($this->loan->type()=='home'):
				$ReportType = '100001';
			else:
				$ReportType = '100002';
			endif;
		 }

	

		$query = array(
			'Key' => $this->getKey(),
			'Address' => $address,
			'LastLine' => $LastLine,
			'OwnerName' => $OwnerName,
			'ReportType' => $ReportType,
			'ClientReference' => $ClientReference
		);


		$soapClient = new SoapClient($this->getWSDL(),array('trace'=>1));

		$soapClient->__setLocation($this->getbaseURL());

		$key = $this->getKey();

		$soapparams = array(new SoapParam($key,"Key"),
											 new SoapParam($address,"Address"),
											 new SoapParam($LastLine,"LastLine"),
											 new SoapParam($ReportType,"ReportType"),
											 new SoapParam($ClientReference,"ClientReference"));

		$result = $soapClient->AddressSearch($query);

		if($result){
		
			$this->_address_obj = $result->AddressSearchResult;

			if($result->AddressSearchResult->Status !== "Invalid IP"):
			if($this->_address_obj->MatchStatus == 'Matched' && $this->_address_obj->StatusCode == 'OK')
			{
				// Store Address Report for retreival.
				if( !empty( $this->_address_obj->ReportXML ) ) 
					$this->_property_report = $this->_address_obj->ReportXML;
				else 
					$this->_property_report = $this->_address_obj->ReportURL;

				$data = simplexml_load_string($this->_property_report->any);
				return $data;
				
			}
			else if($this->_address_obj->MatchStatus == 'Multiple Matches' && $this->_address_obj->StatusCode == 'MM')
			{
				$this->_matches = true;
				// LogAction::debug("Bk: 1");
				//We'll probably store all the matches here and show them? Break for now.
				// Are you here? It's because we haven't handled multiple Matches... Go Fast. Break things... or leave them out.
				$error =  '<div id="Container" style="padding-top:10px; padding-bottom:20px" class="portlet">
						<div class="portlet-body magchat-container" style ="padding-bottom: 40px;">
							<div class="white-cover">
								<img style="max-height:100px;" id="SourceLogo" src="/skin/img/blackknight.png"/>
								<h4>Unfortunately, "'.$this->_address_obj->MatchStatus.'" was found. Blackknight cannot provide information about it.</h4>
							</div>
						</div>
					</div>';
				if($lender == true)
				{
					echo $error;
					exit(0);
				}
				else
					return $error;

			}
			else if($this->_address_obj->Status == 'OK' && $this->_address_obj->StatusCode == 'NM')
			{
				$this->_matches = true; 
				// LogAction::debug("Bk: 2");
				//We'll probably store all the matches here and show them? Break for now.
				// Are you here? It's because we haven't handled multiple Matches... Go Fast. Break things... or leave them out.
				$error = '<div id="Container" style="padding-top:10px; padding-bottom:20px" class="portlet">
						<div class="portlet-body magchat-container" style ="padding-bottom: 40px;">
							<div class="white-cover">
								<img style="max-height:100px;" id="SourceLogo" src="/skin/img/blackknight.png"/>
								<h4>Unfortunately, "'.$this->_address_obj->MatchStatus.'" was found. Blackknight cannot provide information about it.</h4>
							</div>
						</div>
					</div>';
				if($lender == true)
				{
					echo $error;
					exit(0);
				}
				else
					return $error;

			}
			else 
			{
				
				// LogAction::debug("Bk: 3");// Something Bad happened...
				// var_dump($result); //<- Start Here
				$error = '<div id="Container" style="padding-top:10px; padding-bottom:20px" class="portlet">
						<div class="portlet-body magchat-container" style ="padding-bottom: 40px;">
							<div class="white-cover">
								<img style="max-height:100px;" id="SourceLogo" src="/skin/img/blackknight.png"/>
								<h4>Unfortunately, the address provided is "'.$this->_address_obj->Status.'". Blackknight cannot provide information about it.</h4>
							</div>
						</div>
					</div>';
				
				if($lender == true)
				{
					echo $error;
					exit(0);
				}
				else
					return $error;
			}
			else:

			$error = '<div id="Container" style="padding-top:10px; padding-bottom:20px" class="portlet">
						<div class="portlet-body magchat-container" style ="padding-bottom: 40px;">
							<div class="white-cover">
								<img style="max-height:100px;" id="SourceLogo" src="/skin/img/blackknight.png"/>
								<h4>I think, this is a domain issue. Check your domain please.</h4>
							</div>
						</div>
					</div>';
					return $error;
		
			endif;
		}
		else
		{
			// Something Bad happened...
			// var_dump($result); //<- Start Here
			// LogAction::debug("Bk: 4");
			$error =  '<div id="Container" style="padding-top:10px; padding-bottom:20px" class="portlet">
						<div class="portlet-body magchat-container" style ="padding-bottom: 40px;">
							<div class="white-cover">
								<img style="max-height:100px;" id="SourceLogo" src="/skin/img/blackknight.png"/>
								<h4>Unfortunately, the address is either incorrect or is incomplete. Blackknight cannot provide information about it. </h4>
							</div>
						</div>
					</div>';
				if($lender == true)
				{
					echo $error;
					exit(0);
				}
				else
					return $error;
		}

	}

	public function clean_data($str)
	{
		if($str)
		{
			$strlength = strlen($str);
			if($strlength > 0 && $str != "0")
				return true;
			
		}

	}

} ?>