
        <table  class="table table-striped table-condensed table-hover m-t-md">

            <caption class="text-primary" style=" font-size: 1.2em; font-weight: bold; text-align: center; caption-side: top;">Attach Loan Documents</caption>
            <thead>
                <tr>
                    <th>Document type</th>
                    <th>Description</th>
                    <th>File name</th>
                    <th> <a data-bind="click: $root.addLoan_doc" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i></a></th>
                </tr>
            </thead>
           
            <tbody data-bind='foreach: $root.added_loan_doc_type'>
                <tr>
                    <td>
                           <select data-bind='options: $root.loan_doc_types, optionsText: "loan_doc_type",  
                            optionsCaption: "-- No Loan Docs Attached --", value:selected_loan_doc_type' class="form-control"  style="width: 250px"> 
                            </select> 
                        </td>
                        <td data-bind="with: selected_loan_doc_type">
                            <textarea type="text" data-bind='attr:{name:"loan_docs["+$index()+"][description]"}' class="form-control m-b" required="required"></textarea>
                            <input type="hidden" data-bind='attr:{name:"loan_docs["+$index()+"][loan_doc_type_id]"}, value: id'/>  
                            
                        </td>
                        <td data-bind="with: selected_loan_doc_type">
                            <input type="file" data-bind='attr:{name:"file_name[]"}'/>  
                            
                        </td>
                        <td>
                            <span title="Remove Doc" class="btn text-danger" data-bind='click: $root.removeLoan_doc'><i class="fa fa-minus"></i></span>
                        </td>
                </tr>
            </tbody>
        </table>