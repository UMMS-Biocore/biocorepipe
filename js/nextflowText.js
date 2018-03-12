//----------------------------------------------------------------
//creates nextflow text. Requires pipelineD3.js or runpipeline.js
//----------------------------------------------------------------
function gFormat(gText) {
    gPatt = /(.*)-(.*)/
    gText = gText.replace(gPatt, '$1_$2')
    return gText
}

function sortProcessList(processList) {
    //remove inPro, outPro from edges
    var allEdges = edges;
    var mainEdges = [];
    for (var e = 0; e < allEdges.length; e++) {
        if (allEdges[e].indexOf("inPro") === -1 && edges[e].indexOf("outPro") === -1) { //if not exist -1, 
            //swap to make sure, first node is output (for simplification)
            var nodes = allEdges[e].split("_")
            if (nodes[0][0] === "i") {
                mainEdges.push(nodes[1] + '_' + nodes[0]);
            } else {
                mainEdges.push(allEdges[e]);
            }
        }
    }
    var sortGnum = [];
    if (mainEdges.length > 0) {
        for (var e = 0; e < mainEdges.length; e++) { //mainEdges.length
            var patt = /(.*)-(.*)-(.*)-(.*)-(.*)_(.*)-(.*)-(.*)-(.*)-(.*)/;
            var outGnum = '';
            var inGnum = '';
            var outGnum = mainEdges[e].replace(patt, '$5');
            var inGnum = mainEdges[e].replace(patt, '$10');
            //for first raw insert both values
            //first can be added by push but other should be splice
            if (!sortGnum.includes(outGnum)) {
                //if output of input is exist in the array, insert before it.
                if (sortGnum.includes(inGnum)) {
                    var index = sortGnum.indexOf(inGnum);
                    sortGnum.splice(index, 0, outGnum);
                    var index = sortGnum.indexOf(outGnum);
                } else {
                    sortGnum.push(outGnum);
                    var index = sortGnum.indexOf(outGnum);
                }
            } else {
                //check if the position of outGnum if inGnum is also exist in array
                //cut inGnum and paste to indexOut position
                if (sortGnum.includes(inGnum) && sortGnum.includes(outGnum)) {
                    var indexIn = sortGnum.indexOf(inGnum);
                    var indexOut = sortGnum.indexOf(outGnum);
                    if (indexOut > indexIn) {
                        sortGnum.splice(indexIn, 1);
                        sortGnum.splice(indexOut, 0, inGnum);
                    }
                }
                var index = sortGnum.indexOf(outGnum);
            }
            if (!sortGnum.includes(inGnum)) {
                sortGnum.splice(index + 1, 0, inGnum);
                var index = sortGnum.indexOf(inGnum); //last index after insertion
            } else {
                var index = sortGnum.indexOf(inGnum);
            }
            //stop for final edge
            if (e + 1 < mainEdges.length) {
                for (var k = e + 1; k < mainEdges.length; k++) {
                    var outGnum2 = '';
                    var inGnum2 = '';
                    var outGnum2 = mainEdges[k].replace(patt, '$5');
                    var inGnum2 = mainEdges[k].replace(patt, '$10');
                    if (inGnum === outGnum2) {
                        if (!sortGnum.includes(inGnum2)) {
                            sortGnum.splice(index + 1, 0, inGnum2);
                        }
                    }
                }
            }
        }
    }
    var sortProcessList = [];
    $.each(sortGnum, function (el) {
        sortProcessList.push("g-" + sortGnum[el]);
    });
    //add remaining input and output params by using processlist
    for (var key in processList) {
        if (!sortProcessList.includes(key)) {
            sortProcessList.push(key);
        }
    }
    return sortProcessList;
}




function createNextflowFile(nxf_runmode) {
    nextText = "";
    if (nxf_runmode === "run") {
        var publish_dir_check = $('#publish_dir_check').is(":checked").toString();
        if (publish_dir_check === "true") {
            var output_dir = $('#publish_dir').val();
        } else {
            var output_dir = $('#rOut_dir').val();
        }
        if (output_dir) {
            nextText = "params.outdir = '" + output_dir + "' " + " \n\n";
        }
    } else {
        nextText = "params.outdir = 'results' " + " \n\n";
    }
    iniTextSecond = ""
    //sortProcessList
    var sortedProcessList = sortProcessList(processList);
    //initial input data added
    sortedProcessList.forEach(function (key) {
        className = document.getElementById(key).getAttribute("class");
        mainProcessId = className.split("-")[1];
        iniText = InputParameters(mainProcessId, key);
        iniTextSecond = iniTextSecond + iniText.secPart;
        nextText = nextText + iniText.firstPart;
    });
    nextText = nextText + "\n" + iniTextSecond + "\n"

    sortedProcessList.forEach(function (key) {
        className = document.getElementById(key).getAttribute("class");
        mainProcessId = className.split("-")[1]
        if (mainProcessId !== "inPro" && mainProcessId !== "outPro") { //if it is not input parameter print process data
            var body = "";
            var script_header = "";
            [body, script_header] = IOandScriptForNf(mainProcessId, key);
            proText = script_header + "\nprocess " + processList[key] + " {\n\n" + publishDir(mainProcessId, key) + body + "\n\n}\n" + outputFileName(mainProcessId, key) + "\n\n"
            nextText = nextText + proText
        }
    });
    var endText = 'workflow.onComplete {\n';
    endText += 'println "##Pipeline execution summary##"\n';
    endText += 'println "---------------------------"\n';
    endText += 'println "##Completed at: $workflow.complete"\n';
    endText += 'println "##Duration: ${workflow.duration}"\n';
    endText += 'println "##Success: ${workflow.success ? \'OK\' : \'failed\' }"\n';
    endText += 'println "##Exit status: ${workflow.exitStatus}"\n';
    endText += '}\n';


    if (nxf_runmode === "run") {
        var interdel = $('#intermeDel').is(":checked");
        if (interdel && interdel === true) {
            endText = endText + "workflow.onComplete { file('work').deleteDir() } \n";
        }
    }
    return nextText + endText
}

//g_1_genome_index.subscribe {println "##Output:'genome.index*'## ${it.name}"}
function outputFileName(id, currgid) {
    outFileName = "";
    OList = d3.select("#" + currgid).selectAll("circle[kind ='output']")[0];
    for (var o = 0; o < OList.length; o++) {
        Oid = OList[o].id
        outputIdSplit = Oid.split("-")
        //        qual = parametersData.filter(function (el) {
        //            return el.id == outputIdSplit[3]
        //        })[0].qualifier
        outputName = document.getElementById(Oid).getAttribute("name");
        outputName = outputName.replace(/\"/g, '');
        outputName = outputName.replace(/\'/g, '');
        outputName = outputName.replace(/\?/g, '')
        outputName = outputName.replace(/\${(.*)}/g, '*');
        genParName = parametersData.filter(function (el) {
            return el.id == outputIdSplit[3]
        })[0].name
        channelName = gFormat(document.getElementById(Oid).getAttribute("parentG")) + "_" + genParName

        //        outFileName = outFileName + " " + channelName + ".subscribe {println \"##Output:" + outputName + "## ${it.name}\"}" + "\n"
        //        outFileName = outFileName + " " + channelName + ".subscribe {println \"##Output:" + "## ${it.name}\"}" + "\n"
    }
    return outFileName;
}

function getChannelNameAll(channelName, Iid) {
    var channelNameAll = "";
    for (var c = 0; c < edges.length; c++) {
        if (edges[c].indexOf(Iid) !== -1) {
            var secNode = edges[c].split("_")[1];
            if (channelNameAll === "") {
                channelNameAll = channelNameAll + channelName + "_" + gFormat(document.getElementById(secNode).getAttribute("parentG"));
            } else {
                channelNameAll = channelNameAll + "; " + channelName + "_" + gFormat(document.getElementById(secNode).getAttribute("parentG"));
            }

        }
    }
    return channelNameAll
}

//Input parameters and channels with file paths
function InputParameters(id, currgid) {
    IList = d3.select("#" + currgid).selectAll("circle[kind ='input']")[0];
    iText = {};
    firstPart = "";
    secPart = "";

    for (var i = 0; i < IList.length; i++) {
        Iid = IList[i].id
        inputIdSplit = Iid.split("-")
        ProId = inputIdSplit[1]
        userEntryId = "text-" + inputIdSplit[4];

        if (ProId === "inPro" && inputIdSplit[3] !== "inPara") {
            qual = parametersData.filter(function (el) {
                return el.id == inputIdSplit[3]
            })[0].qualifier
            inputParamName = document.getElementById(userEntryId).getAttribute('name')

            for (var e = 0; e < edges.length; e++) {
                if (edges[e].indexOf(Iid) !== -1) { //if not exist: -1
                    nodes = edges[e].split("_")
                    fNode = nodes[0]
                    sNode = nodes[1]
                    inputIdSplit = sNode.split("-")
                    genParName = parametersData.filter(function (el) {
                        return el.id == inputIdSplit[3]
                    })[0].name
                    channelName = gFormat(document.getElementById(fNode).getAttribute("parentG")) + "_" + genParName //g-0-genome

                    //check proId had a mate inputparameter
                    if (qual === "set") {
                        var sNodeProId = inputIdSplit[1];
                        var inputParAll = getValues({ p: "getInputsPP", "process_id": sNodeProId });
                        var inputParMate = inputParAll.filter(function (el) {
                            return el.sname == "mate"
                        }).length
                    }

                    if (qual === "file") {
                        firstPartTemp = "params." + inputParamName + " =\"\" \n"
                        secPartTemp = channelName + " = " + "file(params." + inputParamName + ") \n"
                        firstPart = firstPart + firstPartTemp
                        secPart = secPart + secPartTemp
                        break
                        //if mate defined in process use fromFilePairs
                    } else if (qual === "set" && inputParMate > 0) {
                        firstPartTemp = "params." + inputParamName + " =\"\" \n";
                        //all processes that are connected to
                        var channelNameAll = "";
                        channelNameAll = getChannelNameAll(channelName, Iid);
                        secPartTemp = "Channel\n\t.fromFilePairs( params." + inputParamName + " , size: (params.mate != \"pair\") ? 1 : 2 )\n\t.ifEmpty { error \"Cannot find any " + genParName + " matching: ${params." + inputParamName + "}\" }\n\t.into { " + channelNameAll + "} \n\n";
                        firstPart = firstPart + firstPartTemp
                        secPart = secPart + secPartTemp
                        break
                        //if mate not defined in process use fromPath
                    } else if (qual === "set" && inputParMate === 0) {
                        firstPartTemp = "params." + inputParamName + " =\"\" \n";
                        //all processes that are connected to
                        var channelNameAll = "";
                        channelNameAll = getChannelNameAll(channelName, Iid);
                        secPartTemp = channelNameAll + " = " + "Channel.fromPath(" + inputParamName + ") \n"
                        firstPart = firstPart + firstPartTemp
                        secPart = secPart + secPartTemp
                        break
                    } else if (qual === "val") {
                        firstPartTemp = "params." + inputParamName + " =\"\" \n"
                        secPartTemp = channelName + " = " + "params." + inputParamName + "\n"
                        firstPart = firstPart + firstPartTemp
                        secPart = secPart + secPartTemp
                        break

                    }

                }
            }
        }
    }
    iText.firstPart = firstPart
    iText.secPart = secPart

    return iText
}

function getParamOutdir(outParUserEntry) {
    return '"' + outParUserEntry + '/$filename"';
    // "paired/$filename"
}

//
function getPublishDirRegex(outputName) {
    //eg. set val(name), file("${params.wdir}/validfastq/*.fastq") then take inside of the file(x)
    if (outputName.match(/file\((.*)\)/)) {
        var outputName = outputName.match(/file\((.*)\)/)[1];
    }
    //if name contains path and separated by '/' then take the last part
    if (outputName.match(/\//)) {
        var outArr = outputName.split("/");
        outputName = outArr[outArr.length - 1];
    }

    outputName = outputName.replace(/\*/g, '')
    outputName = outputName.replace(/\?/g, '')
    outputName = outputName.replace(/\'/g, '')
    outputName = outputName.replace(/\"/g, '')
    return outputName;
}

function publishDir(id, currgid) {
    oText = ""
    var closePar = false
    oList = d3.select("#" + currgid).selectAll("circle[kind ='output']")[0]
    for (var i = 0; i < oList.length; i++) {
        oId = oList[i].id
        for (var e = 0; e < edges.length; e++) {
            if (edges[e].indexOf(oId) !== -1) { //if not exist -1, 
                nodes = edges[e].split("_")
                fNode = nodes[0]
                sNode = nodes[1]
                //publishDir Section
                if (fNode.split("-")[1] === "outPro" && closePar === false) {
                    closePar = true
                    //outPro node : get userEntryId and userEntryText
                    var parId = fNode.split("-")[4]
                    var userEntryId = "text-" + fNode.split("-")[4]
                    outParUserEntry = document.getElementById(userEntryId).getAttribute('name');
                    reg_ex = document.getElementById(oId).getAttribute("reg_ex");
                    console.log(reg_ex)
                    if (reg_ex !== "" && reg_ex !== null) {
                            reg_ex = decodeHtml(reg_ex);
                        if (reg_ex.length > 0) {
                            var lastLetter = reg_ex.length - 1;
                            if (reg_ex[0] === "\/" && reg_ex[lastLetter] === "\/") {
                                outputName = reg_ex;
                            } else {
                                outputName = "\/" + reg_ex + "\/";
                            }
                        }

                    } else {
                        outputName = document.getElementById(oId).getAttribute("name")
                        outputName = "/" + getPublishDirRegex(outputName) + "/";
                    }
                    oText = "publishDir params.outdir, mode: 'copy',\n\tsaveAs: {filename ->\n"
                    tempText = "\tif \(filename =~ " + outputName + "\) " + getParamOutdir(outParUserEntry) + "\n"
                    // if (filename =~ /^path.8.fastq$/) filename 
                    oText = oText + tempText
                } else if (fNode.split("-")[1] === "outPro" && closePar === true) {
                    reg_ex = document.getElementById(oId).getAttribute("reg_ex");
                    console.log(reg_ex)
                    if (reg_ex !== "" && reg_ex !== null) {
                            reg_ex = decodeHtml(reg_ex);
                        if (reg_ex.length > 0) {
                            var lastLetter = reg_ex.length - 1;
                            if (reg_ex[0] === "\/" && reg_ex[lastLetter] === "\/") {
                                outputName = reg_ex;
                            } else {
                                outputName = "\/" + reg_ex + "\/";
                            }
                        }
                    } else {
                        outputName = document.getElementById(oId).getAttribute("name")
                        outputName = "/" + getPublishDirRegex(outputName) + "/";
                    }
                    var parId = fNode.split("-")[4]
                    var userEntryId = "text-" + fNode.split("-")[4]
                    outParUserEntry = document.getElementById(userEntryId).getAttribute('name');
                    tempText = "\telse if \(filename =~ " + outputName + "\) " + getParamOutdir(outParUserEntry) + "\n"
                    oText = oText + tempText

                }
            }
        }
    }
    if (closePar === true) {
        oText = oText + "}\n\n";
        if (outputName === '' && reg_ex === "") {
            oText = "publishDir \"${params.outdir}/" + outParUserEntry + "\", mode: 'copy'\n\n";
        }
        closePar = false
    }

    return oText
}



function IOandScriptForNf(id, currgid) {
    var processData = getValues({
        p: "getProcessData",
        "process_id": id
    })
    script = decodeHtml(processData[0].script);
    if (processData[0].script_header !== null){
    var script_header = decodeHtml(processData[0].script_header);
    } else {
        script_header = "";
    }
    var lastLetter = script.length - 1;
    if (script[0] === '"' && script[lastLetter] === '"') {
        script = script.substring(1, script.length - 1); //remove first and last duble quote
    }
    //insert """ for script if not exist
    if (script.search('"""') === -1 && script.search('\'\'\'') === -1) {
        script = '"""\n' + script + '\n"""'
    }



    bodyInput = ""
    bodyOutput = ""
    IList = d3.select("#" + currgid).selectAll("circle[kind ='input']")[0]
    OList = d3.select("#" + currgid).selectAll("circle[kind ='output']")[0]
    for (var i = 0; i < IList.length; i++) {
        if (bodyInput == "") {
            bodyInput = "input:\n"
        }
        Iid = IList[i].id //i-11-0-9-0
        var inputIdSplit = Iid.split("-")
        var qual = parametersData.filter(function (el) {
            return el.id == inputIdSplit[3]
        })[0].qualifier

        var inputName = document.getElementById(Iid).getAttribute("name");
        var inputClosure = document.getElementById(Iid).getAttribute("closure");
        var inputOperator = document.getElementById(Iid).getAttribute("operator");
        inputClosure = decodeHtml(inputClosure);
        var inputOperatorText = '';
        if (inputOperator === 'mode flatten') {
            inputOperatorText = ' ' + inputOperator + inputClosure;
        } else if (inputOperator !== '') {
            if (inputClosure !== '') {
                inputOperatorText = '.' + inputOperator + inputClosure;
            } else if (inputClosure === '') {
                inputOperatorText = '.' + inputOperator + "()";
            }
        }
        find = false
        for (var e = 0; e < edges.length; e++) {
            if (edges[e].indexOf(Iid) > -1) { //if not exist: -1
                find = true
                var nodes = edges[e].split("_")
                var fNode = nodes[0];
                var sNode = nodes[1];

                //output node clicked first
                if (fNode[0] === 'o') {
                    var inputIdSplit = fNode.split("-")
                    var genParName = parametersData.filter(function (el) { return el.id == inputIdSplit[3] })[0].name;
                    var qualNode = parametersData.filter(function (el) { return el.id == inputIdSplit[3] })[0].qualifier;

                    if (qualNode === 'set') {
                        var channelName = gFormat(document.getElementById(fNode).getAttribute("parentG")) + "_" + genParName + "_" + gFormat(document.getElementById(sNode).getAttribute("parentG"));
                    } else {
                        var channelName = gFormat(document.getElementById(fNode).getAttribute("parentG")) + "_" + genParName;
                    }

                } else {
                    var inputIdSplit = sNode.split("-");
                    var genParName = parametersData.filter(function (el) { return el.id == inputIdSplit[3] })[0].name;
                    var qualNode = parametersData.filter(function (el) { return el.id == inputIdSplit[3] })[0].qualifier;
                    if (qualNode === 'set') {
                        var channelName = gFormat(document.getElementById(sNode).getAttribute("parentG")) + "_" + genParName + "_" + gFormat(document.getElementById(fNode).getAttribute("parentG"));
                    } else {
                        var channelName = gFormat(document.getElementById(sNode).getAttribute("parentG")) + "_" + genParName;
                    }
                }
                bodyInput = bodyInput + " " + qual + " " + inputName + " from " + channelName + inputOperatorText + "\n";
            }
        }
        if (find == false) {
            bodyInput = bodyInput + " " + qual + " " + inputName + " from " + "param" + "\n"
        }
    }

    for (var o = 0; o < OList.length; o++) {
        if (bodyOutput == "") {
            bodyOutput = "output:\n"
        }
        Oid = OList[o].id
        outputIdSplit = Oid.split("-")
        qual = parametersData.filter(function (el) {
            return el.id == outputIdSplit[3]
        })[0].qualifier
        outputName = document.getElementById(Oid).getAttribute("name");
        var outputClosure = document.getElementById(Oid).getAttribute("closure");
        var outputOperator = document.getElementById(Oid).getAttribute("operator");
        outputClosure = decodeHtml(outputClosure);
        var outputOperatorText = '';
        if (outputOperator === 'mode flatten') {
            outputOperatorText = ' ' + outputOperator + outputClosure;
        } else if (outputOperator !== '') {
            if (outputClosure !== '') {
                outputOperatorText = '.' + outputOperator + outputClosure;
            } else if (outputClosure === '') {
                outputOperatorText = '.' + outputOperator + "()";
            }
        }
        genParName = parametersData.filter(function (el) {
            return el.id == outputIdSplit[3]
        })[0].name
        channelName = gFormat(document.getElementById(Oid).getAttribute("parentG")) + "_" + genParName;

        //find all edges emerges from this output
        if (qual === "set") {
            var channelNameAll = "";
            for (var c = 0; c < edges.length; c++) {
                if (edges[c].indexOf(Oid) == 0) {
                    var secNode = edges[c].split("_")[1];
                    var secProType = secNode.split("-")[1];
                    if (secProType !== "outPro") {
                        if (channelNameAll === "") {
                            channelNameAll = channelNameAll + channelName + "_" + gFormat(document.getElementById(secNode).getAttribute("parentG"));
                        } else {
                            channelNameAll = channelNameAll + ", " + channelName + "_" + gFormat(document.getElementById(secNode).getAttribute("parentG"));
                        }
                    }

                } else if (edges[c].indexOf(Oid) > 0) {
                    var fstNode = edges[c].split("_")[0];
                    var fstProType = fstNode.split("-")[1];
                    if (fstProType !== "outPro") {
                        if (channelNameAll === "") {
                            channelNameAll = channelNameAll + channelName + "_" + gFormat(document.getElementById(fstNode).getAttribute("parentG"));
                        } else {
                            channelNameAll = channelNameAll + ", " + channelName + "_" + gFormat(document.getElementById(fstNode).getAttribute("parentG"));
                        }
                    }
                }
            }
            // if output node is not connected to input node.
            if (channelNameAll === '') {
                channelNameAll = channelName;
            }
        } else if (qual !== "set") {
            channelNameAll = channelName;
        }
        bodyOutput = bodyOutput + " " + qual + " " + outputName + " into " + channelNameAll + outputOperatorText + "\n"

    }
    body = bodyInput + "\n" + bodyOutput + "\n" + script
    return [body, script_header]
}
