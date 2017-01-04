import java.util.HashMap;
import java.util.List;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.PrintWriter;

import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.nodes.Node;
import org.jsoup.select.Elements;


public class Tester {

	public static void main(String [] args) {
		for(int i = 1; i < 2; i++){
			try{
				File f = new File(".\\train\\"+i);
				System.out.println("train/"+i+"\t");
				if(!f.exists())
					continue;
				try {
					Document doc = Jsoup.parse(f,"UTF-8","http://indiarailinfo/departures/");
					Elements name = doc.getElementsByTag("title");
					String answer = "";
					File file = null;
					for(Element e : name){
						String [] data = e.html().split("/");
						if(data.length < 2)
							continue;
						String [] tname = data[1].split("-");
						file = new File(".\\alltrain\\"+data[0]);
						answer += data[0].trim()+"|"+tname[0].trim()+"|";
					}
			
					if(file == null)
						continue;
					if(!file.exists())
						file.createNewFile();
					else
						continue;
			
					PrintWriter fw = new PrintWriter(new FileOutputStream(file,true));
			
					Elements type = doc.getElementsByClass("listingcapsulehalf");
					Element t = type.get(0);
					type = t.getElementsByClass("genbg");
					t = type.get(1);
					type = t.getElementsByTag("span");
					t = type.get(0);
					answer+=t.html().trim()+"|";
					answer+=type.get(1).html().trim().split("/")[0];
					Elements stations = doc.getElementsByClass("detailsprominent");
					answer +="|"+stations.get(0).html().trim().split("/")[0]+"|"+stations.get(1).html().trim().split("/")[0]+"\n";
					type = doc.getElementsByClass("rake");
					HashMap<String, Integer> rakeinfo = new HashMap<>();
					for(Element e : type){
						if(rakeinfo.containsKey(e.attr("class"))){
							rakeinfo.put(e.attr("class"), rakeinfo.get(e.attr("class"))+1);
						}else
							rakeinfo.put(e.attr("class"), 1);
					}
					for(String d : rakeinfo.keySet()){
						answer += d+"-"+rakeinfo.get(d)+"|";
					}
					answer += "\n";
					type = doc.getElementsByClass("newschtable");
					t = type.get(0);
					type = t.getElementsByTag("tr");
					for(int j = 1; j < type.size(); j+=2){
						t = type.get(j);
						Elements temp = t.getElementsByTag("td");
						if(temp.size() < 8)
							continue;
						answer += getPlainText(temp.get(2))+"|"+getPlainText(temp.get(3))+"|"+getPlainText(temp.get(6))+"|"+getPlainText(temp.get(8))+"|"+getPlainText(temp.get(11))+"|"+getPlainText(temp.get(12))+"|"+getPlainText(temp.get(13))+"|"+getPlainText(temp.get(14))+"|"+getPlainText(temp.get(16))+"|"+getPlainText(temp.get(17))+"\n";
					}
					System.out.println(answer);
					fw.append(answer);
					fw.close();
					System.out.println(file.getName());
				} catch (IOException e) {
					// TODO Auto-generated catch block
					e.printStackTrace()	;
				}
			}catch(Exception e){
				File f1 = new File("Error");
				if(!f1.exists())
					try {
						f1.createNewFile();
					} catch (IOException e1) {
						// TODO Auto-generated catch block
						e1.printStackTrace();
					}
				PrintWriter p = null;
				try {
					p = new PrintWriter(new FileOutputStream(f1,true));
				} catch (FileNotFoundException e1) {
					// TODO Auto-generated catch block
					e1.printStackTrace();
				}
				p.append(i+"\n");
				p.close();
			}
		}
	}
	
	
	public static String getPlainText(Element e){
		String answer = null;
		Elements w = e.getElementsByTag("a");
		if(w.size() == 0){
		w = e.getElementsByTag("b");
		if(w.size() == 0){
			w = e.getElementsByTag("span");
			if(w.size() == 0)
				return (answer = e.html().trim());
			e = w.get(0);
			return (answer = e.html().trim());
		}else {
			e = w.get(0);
			w = e.getElementsByTag("span");
			if(w.size() == 0)
				return (answer = e.html().trim());
			e = w.get(0);
			return (answer = e.html().trim());
		}}
		else{
			e = w.get(0);
			w = e.getElementsByTag("b");
			if(w.size() == 0){
				w = e.getElementsByTag("span");
				if(w.size() == 0)
					return (answer = e.html().trim());
				e = w.get(0);
				return (answer = e.html().trim());
			}else {
				e = w.get(0);
				w = e.getElementsByTag("span");
				if(w.size() == 0)
					return (answer = e.html().trim());
				e = w.get(0);
				return (answer = e.html().trim());
			}
		}
	}
	
}
