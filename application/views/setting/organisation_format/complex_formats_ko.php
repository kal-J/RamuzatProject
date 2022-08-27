    const FormatSection = function(format_cat_id){
            const self = this;
            self.id = null;
            self.format_cat_id = format_cat_id;
            self.section_format = ko.observable();
            self.section_seperator = ko.observable();
            self.section_start = ko.observable();
            self.section_length = ko.observable();
        
    };
    const FormatsModel = function(format_cat){
        const self = this;
        self.format_types= Object.values(<?php echo json_encode($format_types); ?>);
        self.format_options = [{id:1, label:"Static Digits/Letters"},{id:2, label:"Incrementing Letters"},{id:3, label:"Incrementing Digits"}, {id:4, label:"YYYY ("+moment().format("YYYY")+")"}, {id:5, label:"YY ("+moment().format("YY")+")"}, {id:6, label:"MMM ("+moment().format("MMM")+")"}, {id:7, label:"MM ("+moment().format("MM")+")"}, {id:8, label:"ddd ("+moment().format("ddd")+")"}, {id:9, label:"DD ("+moment().format("DD")+")"}];
        self.separators = [{value:"-", label:"-"},{value:"/", label:"/"}, {value:"\\", label:"\\"}, {value:" ", label:"White/Blank Space"}/*, {value:"o", label:"Custom"}*/];
        self.format_sections = ko.observableArray([new FormatSection(format_cat)]);
        self.format_type = ko.observable(self.format_types[format_cat-1]?self.format_types[format_cat-1]:"1");
        self.add_section = function(data, click_event){
            const current_len = self.format_sections().length;
            current_len<9 && self.format_sections.push(new FormatSection(format_cat));
        };
        self.remove_section = function(data, click_event){
            self.format_sections.remove(data);
        };
        self.final_format = ko.computed(function(){
            let final_text = "";
            ko.utils.arrayForEach(self.format_sections(), function(current_format){
                const section_length = current_format.section_length();
                const section_start = current_format.section_start();
                const separator = current_format.section_seperator();
                const sep_symbol = separator? separator:'';
                switch(current_format.section_format()){
                    case 1:
                    case 2:
                        if(section_start && section_start !==''){
                            final_text =  final_text + sep_symbol + section_start;
                        }
                        break;
                    case 3:
                        if(section_length && section_length !=='' && section_start && section_start !==''){
                            final_text =  final_text + sep_symbol + section_start.padStart(section_length,0);
                        }
                        break;
                    case 4:
                        final_text =  final_text + sep_symbol + moment().format("YYYY");
                        break;
                    case 5:
                        final_text =  final_text + sep_symbol + moment().format("YY");
                        break;
                    case 6:
                        final_text =  final_text + sep_symbol + moment().format("MMM");
                        break;
                    case 7:
                        final_text =  final_text + sep_symbol + moment().format("MM");
                        break;
                    case 8:
                        final_text =  final_text + sep_symbol + moment().format("M");
                        break;
                    case 9:
                        final_text =  final_text + sep_symbol + moment().format("ddd");
                        break;
                    case 10:
                        final_text =  final_text + sep_symbol + moment().format("DD");
                        break;
                    case 11:
                        final_text =  final_text + sep_symbol + moment().format("D");
                        break;
                    default:
                        break;
                }
            });
            return final_text;
        });
        //self.section_format.subscribe(function(new_value){console.log(new_value);});
        self.submit_format = function(){
            const url = "<?php echo site_url("organisation_format/set_num_format");?>";
            $.post(
                url,
                {org_id:1, format_cat:format_cat, format_type:self.format_type(), formats:self.format_sections()},
                function (response) {
                    if (response.success) {
                        setTimeout(function () {
                            toastr.success(response.message, "Success!");
                            }, 1000);
                    } else {
                        toastr.error("", "Operation failed. Reason(s):<ol>" + response.message + "</ol>", "Operation Failure!");
                    }
                },
                'json').fail(function (jqXHR, textStatus, errorThrown) {
                network_error(jqXHR, textStatus, errorThrown, $("#myform"));
            });
        }
        self.get_num_formats = function(){
            $.ajax({
                url:"<?php echo site_url("organisation_format/get_num_format");?>",
                type:"POST",
                dataType:"json",
                data:{format_cat},
                beforeSend: function () {
                    //DO SOMETHING, Like Display Loader Icon GIF;
                },
                success: function (response) {
                    //fill the formats as required
                    if(typeof response.num_formats !== "undefined"){
                        self.format_sections.removeAll();
                        ko.utils.arrayForEach(response.num_formats, function(current_format){
                            const format_section = new FormatSection(format_cat);
                            format_section.id = current_format.id;
                            format_section.format_cat_id = format_cat;
                            format_section.section_format(current_format.section_format);
                            format_section.section_seperator(current_format.section_seperator);
                            format_section.section_start(current_format.section_start);
                            format_section.section_length(current_format.section_length);
                            self.format_sections.push(format_section);
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    network_error(jqXHR, textStatus, errorThrown, $("#myform"));
                }
            });
        }
        self.get_num_formats();
    };
