 if (field001 != null && field004 != null) {
				continue record;
				// the new holding records have 001 and 004. The 001 has a wrong id
				// just skip it, we don't need any data
			}
			 
			 /*if (field001 != null) {
				 Element objectIdElement = doc.createElement("objectid");
				 recordEl.appendChild(objectIdElement);
				 objectIdElement.appendChild(doc.createTextNode(field001.getData()));
				 object_id = field001.getData();
			 }
			 
			 List<DataField> datafields = record.getDataFields();
			 
			 for (DataField datafield : datafields) {
				 
				 if (datafield.getTag().equalsIgnoreCase("245")) {
					 
					 Element dataElement = doc.createElement("title");
					 recordEl.appendChild(dataElement);
					 dataElement.appendChild(doc.createTextNode(datafield.getSubfield('a').getData()));
					 
					 /*dataElement = doc.createElement("subject");
					 recordEl.appendChild(dataElement);
					 dataElement.appendChild(doc.createTextNode(datafield.getSubfield('a').getData()));*/
					
				/*} else if (datafield.getTag().equalsIgnoreCase("856")) {
					if(datafield.getIndicator1() == '4' && datafield.getIndicator2() == '0') {
						String data = "";
						boolean inDigitool = false;
						String pid = datafield.getSubfield('u').getData().split("pid=")[1];
						
						if (pid.startsWith("IE")) {
							// We already have an IE
							data = pid;
						} else {
							for (String pidtemp : entriesDtl.values()) {
								if(pidtemp.equalsIgnoreCase(pid)) {
									// IE is known
									data = pidtemp;
									inDigitool = true;
								}
							}
						}
						if (!inDigitool) {
							// TODO This is temporary, remove me. This is for testing
							data = pid;
						}
						
						Element dataElement = doc.createElement("image");
						 recordEl.appendChild(dataElement);
						 dataElement.appendChild(doc.createTextNode(data)); 	
					}
				/*} else if ((datafield.getTag().equalsIgnoreCase("260") && datafield.getIndicator1() == ' ' && datafield.getIndicator2() == ' ')
						|| (datafield.getTag().equalsIgnoreCase("264") && datafield.getIndicator1() == ' ' && datafield.getIndicator2() == '1')) {
					
						Element dataElement = doc.createElement("Date");
						 recordEl.appendChild(dataElement);
						 dataElement.appendChild(doc.createTextNode(datafield.getSubfield('c').getData().replace(".", ""))); 	
                */
				} else if (datafield.getTag().equalsIgnoreCase("700") 
					&& (datafield.getSubfield('4').getData().equalsIgnoreCase("stu") || datafield.getSubfield('4').getData().equalsIgnoreCase("pfs")
					|| datafield.getSubfield('4').getData().equalsIgnoreCase("aow") || datafield.getSubfield('4').getData().equalsIgnoreCase("aut")
					|| datafield.getSubfield('4').getData().equalsIgnoreCase("egr") || datafield.getSubfield('4').getData().equalsIgnoreCase("etc")
					|| datafield.getSubfield('4').getData().equalsIgnoreCase("ill") || datafield.getSubfield('4').getData().equalsIgnoreCase("oth")
					|| datafield.getSubfield('4').getData().equalsIgnoreCase("prt"))) {
					//a b (c) (d) (q) (g)(4)? (3)
					// This construction is to remove duplicated entries. (thanks to the property of hashset, linked, because order matters
					LinkedHashSet<String> data = new LinkedHashSet<String>();
					Element dataElement = null;
					
					if (datafield.getSubfield('a') != null) {
						data.add(datafield.getSubfield('a').getData());
					}
					if (datafield.getSubfield('b') != null) {
						data.add(" "+ datafield.getSubfield('b').getData());
					}
					if (datafield.getSubfield('c') != null) {
						data.add(" ("+ datafield.getSubfield('c').getData() +  ")");
					}
					if (datafield.getSubfield('d') != null) {
						data.add(" ("+ datafield.getSubfield('d').getData() +  ")");
					}
					if (datafield.getSubfield('q') != null) {
						data.add(" ("+ datafield.getSubfield('q').getData() +  ")");
					}
					if (datafield.getSubfield('g') != null) {
						data.add(" ("+ datafield.getSubfield('g').getData() +  ")");
					}
					if (datafield.getSubfield('3') != null) {
						data.add(" ("+ datafield.getSubfield('3').getData() +  ")");
					}
					
					if ((!datafield.getSubfield('4').getData().equalsIgnoreCase("stu")) 
					&& (!datafield.getSubfield('4').getData().equalsIgnoreCase("pfs"))) {
						switch (datafield.getSubfield('4').getData()) {
						case "aow":
							data.add(" (author of original work)");
							break;
						case "aut":
							data.add(" (author)");
							break;
						case "egr":
							data.add(" (engraver)");
							break;
						case "etc":
							data.add(" (etcher)");
							break;
						case "ill":
							data.add(" (illustrator)");
							break;
						case "oth":
							data.add(" (role not identified)");
							break;
						case "prt":
							data.add(" (printer)");
							break;
						default:
							System.out.println("$4 not catched:" + datafield.getSubfield('4').getData());
						}
					}
					
					if (datafield.getSubfield('4').getData().equalsIgnoreCase("stu")) {
						
						dataElement = doc.createElement("Creator");
						
					} else if (datafield.getSubfield('4').getData().equalsIgnoreCase("pfs")) {
						
						dataElement = doc.createElement("Professor");
						 
					} else {
						dataElement = doc.createElement("Contributor");
					}
					// here we create on giant string
					String outputData = "";
					for (String temp : data) {
						outputData+= temp;
					}
					 recordEl.appendChild(dataElement);
					 dataElement.appendChild(doc.createTextNode(outputData));
					 	
				/*} else if (datafield.getTag().equalsIgnoreCase("952")) {
					if(datafield.getIndicator1() == ' ' && datafield.getIndicator2() == ' ') {
						String data = datafield.getSubfield('d').getData();
						if (datafield.getSubfield('f') != null) {
							data += "; " + datafield.getSubfield('f').getData();
						}
						Element dataElement = doc.createElement("Provenance");
						recordEl.appendChild(dataElement);
						dataElement.appendChild(doc.createTextNode(data)); 
					}
					
				/*} else if (datafield.getTag().equalsIgnoreCase("830")) {
					
					Element dataElement = doc.createElement("Content");
					recordEl.appendChild(dataElement);
					dataElement.appendChild(doc.createTextNode(datafield.getSubfield('a').getData()));
					*/
				/*} else if (datafield.getTag().equalsIgnoreCase("505")) {
					
					String data = "";
					if (datafield.getSubfield('a') != null) {
						data = datafield.getSubfield('a').getData();
					} else {
						System.out.print("nakijken505" + object_id + " ");
						for (Subfield subfield : datafield.getSubfields()) {
							System.out.print(subfield.getData());
						}
						System.out.println("");
					}
					
					if (datafield.getSubfield('g') != null) {
						data += " (" + datafield.getSubfield('g').getData() + ")";
					}
					Element dataElement = doc.createElement("TableOfContents");
					recordEl.appendChild(dataElement);
					dataElement.appendChild(doc.createTextNode(data)); 
					
				/*} else if (datafield.getTag().equalsIgnoreCase("246")) {
					
					Element dataElement = doc.createElement("OtherTitles");
					recordEl.appendChild(dataElement);
					dataElement.appendChild(doc.createTextNode(datafield.getSubfield('a').getData()));
					*/
				/*} else if (datafield.getTag().equalsIgnoreCase("300")) {
					//a : b ; c 
					if(datafield.getIndicator1() == ' ' && datafield.getIndicator2() == ' ') {
					
						String data = datafield.getSubfield('a').getData();
						
						if (datafield.getSubfield('b') != null) {
							data += " : " + datafield.getSubfield('b').getData();
						}
						if (datafield.getSubfield('c') != null) {
							
							// if b is null, there is no need for a ; because subfield a ends with a ;
							if (datafield.getSubfield('b') != null) {
								data += " ; ";
							}
							
							data += datafield.getSubfield('c').getData();
						}
						Element dataElement = doc.createElement("Description");
						recordEl.appendChild(dataElement);
						dataElement.appendChild(doc.createTextNode(data)); 
					}

				} else if (datafield.getTag().equalsIgnoreCase("950")) {
					//a b (c) 
					if(datafield.getIndicator1() == ' ' && datafield.getIndicator2() == ' ') {
						String data = "";
						if (datafield.getSubfield('a') != null) {
							data = datafield.getSubfield('a').getData();
						} else {
							data = "nakijken950" + object_id;
						}
						
						if (datafield.getSubfield('b') != null) {
							data += " " + datafield.getSubfield('b').getData();
						}
						if (datafield.getSubfield('c') != null) {
							data += " (" + datafield.getSubfield('c').getData() + ")";
						}
						Element dataElement = doc.createElement("Illustrations");
						recordEl.appendChild(dataElement);
						dataElement.appendChild(doc.createTextNode(data)); 
					}	
				/*} else if (datafield.getTag().equalsIgnoreCase("500")) {
					if(datafield.getIndicator1() == ' ' && datafield.getIndicator2() == ' ') {
						Element dataElement = doc.createElement("Notes");
						recordEl.appendChild(dataElement);
						if (datafield.getSubfield('a') != null) {
							dataElement.appendChild(doc.createTextNode(datafield.getSubfield('a').getData()));
						} else {
							System.out.println("nakijken500" + object_id);
						}
						
					}
				/*} else if (datafield.getTag().equalsIgnoreCase("544")) {
					Element dataElement = doc.createElement("Source");
					recordEl.appendChild(dataElement);
					dataElement.appendChild(doc.createTextNode(datafield.getSubfield('a').getData()));
					
					if (datafield.getSubfield('b') != null) {
						dataElement = doc.createElement("IdentifierCallnumber");
						recordEl.appendChild(dataElement);
						dataElement.appendChild(doc.createTextNode(datafield.getSubfield('b').getData()));
					} else {
						System.out.println("nakijken544b" + object_id);
					}
					
					
				/*} else if (datafield.getTag().equalsIgnoreCase("852")) {
					
					if (datafield.getSubfield('l') == null) {
						
						String data = datafield.getSubfield('b').getData();
						switch (data) {
						case "BIBC":
							data = "KU Leuven. Division of Heritage & Culture";
							break;
						case "GBIB":
							data = "KU Leuven. Maurits Sabbe Library (Theology)";
							break;
						case "WBIB":
							data = "KU Leuven. Campuslibrary Arenberg";
							break;
						default:
							throw new IllegalArgumentException("Unknown library");
						}//end switch
						
						Element dataElement = doc.createElement("Source");
						recordEl.appendChild(dataElement);
						dataElement.appendChild(doc.createTextNode(data));
						
						if (datafield.getSubfield('h') != null) {
							dataElement = doc.createElement("IdentifierCallnumber");
							recordEl.appendChild(dataElement);
							dataElement.appendChild(doc.createTextNode(datafield.getSubfield('h').getData()));
						}	
					}
				/*} else if (datafield.getTag().equalsIgnoreCase("546")) {

					String data = datafield.getSubfield('a').getData();
					
					Element dataElement = doc.createElement("Language");
					recordEl.appendChild(dataElement);
					dataElement.appendChild(doc.createTextNode(data));
					
				} else if (datafield.getTag().equalsIgnoreCase("653") 
			   && datafield.getIndicator1() == ' ' && datafield.getIndicator2() == '6') {

					String data = datafield.getSubfield('a').getData();
					
					Element dataElement = doc.createElement("Type");
					recordEl.appendChild(dataElement);
					dataElement.appendChild(doc.createTextNode(data));
				}
				 
			 } // end of dataelement loop
