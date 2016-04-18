package convertMARCtoXML;

import java.io.BufferedWriter;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStreamWriter;
import java.net.MalformedURLException;
import java.util.ArrayList;
import java.util.List;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerConfigurationException;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;

import org.marc4j.MarcReader;
import org.marc4j.MarcXmlReader;
import org.marc4j.marc.ControlField;
import org.marc4j.marc.DataField;
import org.marc4j.marc.Record;
import org.marc4j.marc.Subfield;
import org.supercsv.io.CsvListWriter;
import org.supercsv.prefs.CsvPreference;
import org.w3c.dom.Document;
import org.w3c.dom.Element;

public class convertMARCtoXML {

	public static void main(String[] args) throws MalformedURLException {
		FileWriter fstream = null;
		InputStream in = null;
		org.supercsv.io.CsvListWriter csvWriter = null;
		
		try {
			in = new FileInputStream("samen.xml");
			
			csvWriter = new CsvListWriter(new BufferedWriter(new OutputStreamWriter(new FileOutputStream(new File("out.csv")))), CsvPreference.EXCEL_NORTH_EUROPE_PREFERENCE);
		} catch (FileNotFoundException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		DocumentBuilderFactory docFactory = DocumentBuilderFactory.newInstance();
 		DocumentBuilder docBuilder = null;
 		docFactory.setNamespaceAware(false);
 		try {
			docBuilder = docFactory.newDocumentBuilder();
		} catch (ParserConfigurationException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
 		
		MarcReader reader = new MarcXmlReader(in);
		List<Document> docList = new ArrayList<Document>();
		Document doc = docBuilder.newDocument();
		
		Element records = doc.createElement("records");
		doc.appendChild(records);
		 while (reader.hasNext()) {
			 
			 
		 	 Element recordEl = doc.createElement("record");
		 	records.appendChild(recordEl);
			 
			 Record record = reader.next();
			 
			 List<ControlField> controlfields = record.getControlFields();
			 List<Element> elementList = new ArrayList<Element>();
			 
			 
			 for (ControlField controlField : controlfields) {
				
				 String tag = controlField.getTag();
				 String xmlControl = "";
				 if (tag.equalsIgnoreCase("001")
				  || tag.equalsIgnoreCase("004")
				  || tag.equalsIgnoreCase("005")) {
					 xmlControl= "dc_identifier";
				} else if (tag.equalsIgnoreCase("008")) {
					xmlControl= "dc_date";
				} else if (tag.equalsIgnoreCase("d561__a")) {
					xmlControl= "dcterms_provenance";
				} else if (tag.equalsIgnoreCase("d563__3")
				       || tag.equalsIgnoreCase("d563__a")) {
					xmlControl= "dc_description";
				} else {
					xmlControl = tag;
				}
				 
				 String data = controlField.getData();
				 
				 Element controlElement = doc.createElement(xmlControl);
				 recordEl.appendChild(controlElement);
				 controlElement.appendChild(doc.createTextNode(data));
				 
				 elementList.add(controlElement);
				
				 try {
					csvWriter.write(xmlControl);
				} catch (IOException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
				 
			  }
			 
			 List<DataField> datafields = record.getDataFields();
			 
			 for (DataField datafield : datafields) {
				
				 char ind1 = datafield.getIndicator1();
				 char ind2 = datafield.getIndicator2();
				 
				 List<Subfield> subfields = datafield.getSubfields();
				 String tag = datafield.getTag();
				 
				 for (int i = 0; i < subfields.size(); i++) {
					 
					 
					if ( !(Character.toString(ind1).matches(".*\\d.*"))) {
						ind1 = '_';
					}
					if (!(Character.toString(ind2).matches(".*\\d.*"))) {
						ind2 = '_';
					}
					
					String xmlElement = "d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode();
					
					if (("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d24500a")
						|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d24510a")
						|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d2461_a")){
						xmlElement = "dc_title";
						
					} else if (("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d500__9")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d500__a") ) {
						xmlElement = "dc_description";
						
					}else if (("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d5050_a")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d5050_f") 
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d5050_g") 
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d5050_r") 
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d505__a")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d505__g")) {
						xmlElement = "dc_description";
					} else if (("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d035__a")
							||("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d85640u")
							||("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d85640y")
							||("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d952__d")
							||("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d952__e")) {
						
						xmlElement = "dc_identifier";
					} else if (("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d041__a")
							||("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d546__a")
							) {
						xmlElement = "dc_language";
					} else if (("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d24613a")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d246333")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d24633a")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d2463_a")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d24613b")
							) {
						xmlElement = "dcterms_alternative";
					} else if (("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d260_1c")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d260__c")) {
						xmlElement = "dc_publisher";
					} else if (("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d300__a")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d300__b")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d300__c")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d300__e")) {
						xmlElement = "dcters_extent";
					} else if (("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d340__e")) {
						xmlElement = "dc_format";
					}else if (("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d650_72")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d650_7a")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d650_7x")
							) {
						xmlElement = "dc_subject";
					}else if (("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d7001_0")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d7001_3")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d7001_4")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d7001_a")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d7001_c")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d7001_d")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d7001_g")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d7001_q")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d7001_s")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d700__4")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d700__a")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d700__g")
							) {
						xmlElement = "dc_contributor";
					}else if (("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d7102_4")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d7102_a")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d7102_c")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d7102_d")) {
						xmlElement = "dc_creator";
					}else if (("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d8528_b")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d8528_c")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d8528_h")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d852__b")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d852__c")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d852__h")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d852__k")
							|| ("d"+tag + ""+ ind1 + ""+ ind2 + ""+subfields.get(i).getCode()).equalsIgnoreCase("d852__l")
							) {
						xmlElement = "dc_source";
					} 
					
					
					 Element dataElement = doc.createElement(xmlElement);
					 recordEl.appendChild(dataElement);
					 dataElement.appendChild(doc.createTextNode(subfields.get(i).getData()));
					 
					 
					 elementList.add(dataElement);
					 
					 try {
						csvWriter.write(xmlElement);
					} catch (IOException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
					 
				}
			}
			 docList.add(doc);
			 
		 }
		 TransformerFactory transformerFactory = TransformerFactory.newInstance();
         Transformer transformer = null;
         try {
			transformer = transformerFactory.newTransformer();
		} catch (TransformerConfigurationException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
         
      	 try {
      		fstream = new FileWriter("toconvertocsv.xml");
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		 
      	StreamResult result = new StreamResult(fstream);
		 
		transformer.setOutputProperty(OutputKeys.INDENT,"yes");
  		transformer.setOutputProperty("{http://xml.apache.org/xslt}indent-amount", "4");
      	 
  		DOMSource source = new DOMSource(doc);
		
  		try {
			transformer.transform(source, result);
		} catch (TransformerException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} 
	}
}
