	  /**
	   * Extend the Array object
	   * @param candid The string to search for
	   * @returns Returns the index of the first match or -1 if not found
	   */
	  Array.prototype.searchFor = function (candid) {
	      for (var i = 0; i < this.length; i++)
	          if (this[i].indexOf(candid) > -1)
	              return true;
	      return false;
	  };

	  function dragStart(event) {
	      event.dataTransfer.setData("Text", event.target.id);
	  }

	  function dragging(event) {
	      event.preventDefault();
	  }

	  function allowDrop(event) {
	      event.preventDefault();
	  }

	  refreshDataset()

	  function refreshDataset() {
	      processData = getValues({
	          p: "getProcessData"
	      })
	      savedData = getValues({
	          p: "getSavedPipelines"
	      })
	      addOption2LoadSelect()
	      parametersData = getValues({
	          p: "getParametersData"
	      })

	  }
	  var sData = "";
	  var svg = "";
	  var mainG = "";

	  function createSVG() {
	      edges = []
	      w = '100%'
	      h = 500
	      r = 70
	      cx = 0
	      cy = 0
	      ior = r / 6
	      var dat = [{
	          x: 0,
	          y: 0
	      }]
	      gNum = 0
	      selectedgID = ""
	      selectedg = ""
	      diffx = 0
	      diffy = 0

	      processList = {}
	      edges = []
	      candidates = []
	      saveNodes = []

	      dupliPipe = false
	      binding = false
	      renameTextID = ""
	      deleteID = ""

	      d3.select("#svg").remove();
	      //--Pipeline details table clean --
	      $('#inputsTable').find("tr:gt(0)").remove();
	      $('#outputsTable').find("tr:gt(0)").remove();
	      $('#processTable').find("tr:gt(0)").remove();

	      svg = d3.select("#container").append("svg")
	          .attr("id", "svg")
	          .attr("width", w)
	          .attr("height", h)
	          .on("mousedown", startzoom)
	          .on("mouseup", autosave)
	      //	          .call(zoom)
	      mainG = d3.select("#container").select("svg").append("g")
	          .attr("id", "mainG")
	          .attr("transform", "translate(" + 0 + "," + 0 + ")")
	  }

	  function startzoom() {
	      d3.select("#container").call(zoom)
	  }

	  $('#pipelineSum').keyup(function () {
	      autosave();
	  });
	  $("#pipeline-title").keyup(function () { //Click outside of the field or enter
	      autosave();
	  });

	  var timeoutId = 0;

	  function autosave() {}

	  function newPipeline() {
	      createSVG()
	      $('#pipeline-title').val('');
	      $('#pipeline-title').attr('pipelineid', '');
	      resizeForText.call($inputText, $inputText.attr('placeholder'));
	  }

	  function duplicatePipeline() {
	      dupliPipe = true
	      save()
	  }

	  function delPipeline() {
	      var pipeID = $('#pipeline-title').attr('pipelineid');
	      var s = getValues({
	          p: "removePipelineById",
	          'id': pipeID
	      });
	      window.location.replace("index.php?np=1");

	  }


	  function openPipeline(id) {
	      createSVG()
	      sData = getValues({
	          p: "loadPipeline",
	          id: id
	      }) //all data from biocorepipe_save table

	      if (Object.keys(sData).length > 0) {

	          nodes = sData[0].nodes
	          nodes = JSON.parse(nodes.replace(/'/gi, "\""))
	          mG = sData[0].mainG
	          mG = JSON.parse(mG.replace(/'/gi, "\""))["mainG"]
	          zoom.translate([parseFloat(mG[0]), parseFloat(mG[1])]).scale(parseFloat(mG[2]));
	          newTransform = "translate(" + (parseFloat(mG[0])) + "," + (parseFloat(mG[1])) + ")scale(" + (parseFloat(mG[2])) + ")"
	          d3.select("#mainG").attr("transform", newTransform)
	          for (var key in nodes) {
	              x = nodes[key][0]
	              y = nodes[key][1]
	              pId = nodes[key][2]
	              name = nodes[key][3]
	              gN = key.split("-")[1]
	              loadPipeline(x, y, pId, name, gN)
	          }

	          ed = sData[0].edges
	          ed = JSON.parse(ed.replace(/'/gi, "\""))["edges"]
	          for (var ee = 0; ee < ed.length; ee++) {
	              eds = ed[ee].split("_")
	              addCandidates2DictForLoad(eds[0])
	              createEdges(eds[0], eds[1])
	          }
	      }
	      checkReadytoRun();
	  }

	  d3.select("#container").style("background-image", "url(http://68.media.tumblr.com/afc0c91aac9ccc5cbe10ff6f922f58dc/tumblr_nlzk53d4IQ1tagz2no6_r1_500.png)").on("keydown", cancel).on("mousedown", cancel)

	  var zoom = d3.behavior.zoom()
	      .translate([0, 0])
	      .scale(1)
	      .scaleExtent([0.15, 2])
	      .on("zoom", zoomed);

	  createSVG()

	  function zoomed() {
	      mainG.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
	      //d3.select("#startArea").attr("width", 2*(r+ior)* d3.event.scale).attr("height",2*(r+ior)* d3.event.scale)
	  }



	  function addOption2LoadSelect() {
	      for (var i = 0; i < savedData.length; i++) {
	          d3.select("#pipelines").append("option")
	              .attr("value", savedData[i].name)
	              .attr("id", savedData[i].id)
	              .text(savedData[i].name)
	      }
	  }
	  //kind=input/output
	  //
	  function drawParam(name, process_id, id, kind, sDataX, sDataY, paramid, pName, classtoparam, init, pColor) {
	      //gnum uniqe, id same id (Written in class) in same type process
	      g = d3.select("#mainG").append("g")
	          .attr("id", "g-" + gNum)
	          .attr("class", "g-" + id)
	          .attr("transform", "translate(" + sDataX + "," + sDataY + ")")
	      //	          .on("mouseover", mouseOverG)
	      //	          .on("mouseout", mouseOutG)

	      //gnum(written in id): uniqe, id(Written in class): same id in same type process, bc(written in type): same at all bc
	      //outermost circle transparent
	      g.append("circle").attr("id", "bc-" + gNum)
	          .attr("class", "bc-" + id)
	          .attr("type", "bc")
	          .attr("cx", cx)
	          .attr("cy", cy)
	          .attr("r", ipR + ipIor)
	          .attr('fill-opacity', 0)
	          .attr("fill", "#E0E0E0")

	      //second outermost circle visible gray
	      g.append("circle")
	          .datum([{
	              cx: 0,
	              cy: 0
                }])
	          .attr("id", "sc-" + gNum)
	          .attr("class", "sc-" + id)
	          .attr("type", "sc")
	          .attr("r", ipR + ipIor)
	          .attr("fill", "#E0E0E0")
	          .attr('fill-opacity', 1)
	      //	          .on("mouseover", scMouseOver)
	      //	          .on("mouseout", scMouseOut)
	      //	          .call(drag)

	      //gnum(written in id): uniqe, id(Written in class): same id in same type process, bc(written in type): same at all bc
	      //inner parameter circle 


	      d3.select("#g-" + gNum).append("circle")
	          .attr("id", init + "-" + id + "-" + 1 + "-" + paramid + "-" + gNum) //değişecek
	          .attr("type", "I/O")
	          .attr("kind", kind) //connection candidate=input
	          .attr("parentG", "g-" + gNum)
	          .attr("name", name)
	          .attr("status", "standard")
	          .attr("connect", "single")
	          .attr("class", classtoparam)
	          .attr("cx", cx)
	          .attr("cy", cy)
	          .attr("r", ipIor)
	          .attr("fill", pColor)
	          .attr('fill-opacity', 0.8)
	          .on("mouseover", IOmouseOver)
	          .on("mousemove", IOmouseMove)
	          .on("mouseout", IOmouseOut)
	      //	          .on("mousedown", IOconnect)

	      //gnum(written in id): unique,
	      g.append("text").attr("id", "text-" + gNum)
	          .datum([{
	              cx: 0,
	              cy: 20,
	              "name": name
                }])
	          .attr('font-family', "FontAwesome, sans-serif")
	          .attr('font-size', '1em')
	          .attr('name', name)
	          .attr('class', 'inOut')
	          .text(truncateName(name, 'inOut'))
	          .attr("text-anchor", "middle")
	          .attr("x", 0)
	          .attr("y", 28)
	  }

	  function insertRowTable(rowType, firGnum, secGnum, paramGivenName, paraIdentifier, paraFileType, paraQualifier, processName, button) {
	      return '<tr id=' + rowType + 'Ta-' + firGnum + '><td id="' + rowType + '-PName-' + firGnum + '" scope="row">' + paramGivenName + '</td><td>' + paraIdentifier + '</td><td>' + paraFileType + '</td><td>' + paraQualifier + '</td><td> <span id="proGName-' + secGnum + '">' + processName + '</span></td><td>' + button + '</td></tr>'
	  }

	  function insertProRowTable(process_id, gNum, procName, procQueDef, procMemDef, procCpuDef, procTimeDef) {
	      return '<tr procgnum=' + gNum + ' id=procTa-' + process_id + '><td>' + procName + '</td><td><input name="queue" class="form-control" type="text" value="' + procQueDef + '"></input></td><td><input class="form-control" type="text" name="memory" value="' + procMemDef + '"></input></td><td><input name="cpu" class="form-control" type="text" value="' + procCpuDef + '"></input></td><td><input name="cpu" class="form-control" type="text" value="' + procTimeDef + '"></input></td></tr>'
	  }

	  //--Pipeline details table --
	  function addProPipeTab(process_id, gNum) {
	      var procData = processData.filter(function (el) { return el.id == process_id });
	      var procName = procData[0].name;
	      //	      var procQueDef = 'long';
	      //	      var procMemDef = '32024'
	      //	      var procCpuDef = '2';
	      //	      var procTimeDef = '3040';
	      var procQueDef = 'short';
	      var procMemDef = '10 GB'
	      var procCpuDef = '2';
	      var procTimeDef = '100';
	      var proRow = insertProRowTable(process_id, gNum, procName, procQueDef, procMemDef, procCpuDef, procTimeDef);
	      $('#processTable > tbody:last-child').append(proRow);
	  }

	  function removeProPipeTab(id) {
	      var proExist = '';
	      var proExist = $(".g-" + id)[1];
	      //there should be at least 2 process before delete, otherwise delete
	      if (!proExist) {
	          $('#procTa-' + id).remove();
	      }
	  }

	  //	  function addProcess(processDat, xpos, ypos) {
	  //	      t = d3.transform(d3.select('#' + "mainG").attr("transform")),
	  //	          x = (xpos - t.translate[0])
	  //	      y = (ypos - t.translate[1])
	  //	      z = t.scale[0]
	  //
	  //
	  //	      //var process_id = processData[index].id;
	  //
	  //	      //for input parameters:  
	  //	      if (processDat === "inputparam@inPro") {
	  //	          var name = processDat.split('@')[0]
	  //	          var process_id = processDat.split('@')[1]
	  //	          var id = process_id
	  //	          ipR = 70 / 2
	  //	          ipIor = ipR / 3
	  //	          var kind = "input"
	  //	          var sDataX = (5 + x + ipR + ipIor) / z
	  //	          var sDataY = (20 + y + ipR + ipIor) / z
	  //	          var pName = pName || "inputparam"
	  //	          var paramId = paramId || "inPara"
	  //	          var classtoparam = classtoparam || "connect_to_input output"
	  //	          var init = "o"
	  //	          var pColor = "orange"
	  //
	  //	          drawParam(name, process_id, id, kind, sDataX, sDataY, paramId, pName, classtoparam, init, pColor)
	  //	          processList[("g-" + gNum)] = name
	  //	          gNum = gNum + 1
	  //	      }
	  //	      //for output parameters:  
	  //	      else if (processDat === "outputparam@outPro") {
	  //	          var name = processDat.split('@')[0]
	  //	          var process_id = processDat.split('@')[1]
	  //	          var id = process_id
	  //	          ipR = 70 / 2
	  //	          ipIor = ipR / 3
	  //	          var kind = "output"
	  //	          var sDataX = (5 + x + ipR + ipIor) / z
	  //	          var sDataY = (20 + y + ipR + ipIor) / z
	  //	          var pName = pName || "outputparam"
	  //	          var paramId = paramId || "outPara"
	  //	          var classtoparam = classtoparam || "connect_to_output input"
	  //	          var init = "i"
	  //	          var pColor = "green"
	  //	          drawParam(name, process_id, id, kind, sDataX, sDataY, paramId, pName, classtoparam, init, pColor)
	  //
	  //	          processList[("g-" + gNum)] = name
	  //	          gNum = gNum + 1
	  //	      }
	  //
	  //	      //for processes:
	  //	      else {
	  //	          var name = processDat.split('@')[0]
	  //	          var process_id = processDat.split('@')[1]
	  //	          var id = process_id
	  //
	  //	          //--Pipeline details table add process--
	  //	          addProPipeTab(id, gNum)
	  //
	  //	          var inputs = getValues({
	  //	              p: "getInputsPP",
	  //	              "process_id": process_id
	  //	          })
	  //
	  //	          var outputs = getValues({
	  //	              p: "getOutputsPP",
	  //	              "process_id": process_id
	  //	          })
	  //	          //gnum uniqe, id same id (Written in class) in same type process
	  //	          g = d3.select("#mainG").append("g")
	  //	              .attr("id", "g-" + gNum)
	  //	              .attr("class", "g-" + id)
	  //	              .attr("transform", "translate(" + (-30 + x + r + ior) / z + "," + (-10 + y + r + ior) / z + ")")
	  //
	  //	              .on("mouseover", mouseOverG)
	  //	              .on("mouseout", mouseOutG)
	  //	          //gnum(written in id): uniqe, id(Written in class): same id in same type process, bc(written in type): same at all bc
	  //	          g.append("circle").attr("id", "bc-" + gNum)
	  //	              .attr("class", "bc-" + id)
	  //	              .attr("type", "bc")
	  //	              .attr("cx", cx)
	  //	              .attr("cy", cy)
	  //	              .attr("r", r + ior)
	  //	              //  .attr('fill-opacity', 0.6)
	  //	              .attr("fill", "red")
	  //	              .transition()
	  //	              .delay(500)
	  //	              .duration(3000)
	  //	              .attr("fill", "#E0E0E0")
	  //	          //gnum(written in id): uniqe, id(Written in class): same id in same type process, sc(written in type): same at all bc
	  //	          g.append("circle")
	  //	              .datum([{
	  //	                  cx: 0,
	  //	                  cy: 0
	  //                }])
	  //	              .attr("id", "sc-" + gNum)
	  //	              .attr("class", "sc-" + id)
	  //	              .attr("type", "sc")
	  //	              .attr("r", r - ior)
	  //	              .attr("fill", "#BEBEBE")
	  //	              .attr('fill-opacity', 0.6)
	  //	              .on("mouseover", scMouseOver)
	  //	              .on("mouseout", scMouseOut)
	  //	              .call(drag)
	  //	          //gnum(written in id): uniqe,
	  //	          g.append("text").attr("id", "text-" + gNum)
	  //	              .datum([{
	  //	                  cx: 0,
	  //	                  cy: 0
	  //                }])
	  //	              .attr('font-family', "FontAwesome, sans-serif")
	  //	              .attr('font-size', '1em')
	  //	              .attr('name', name)
	  //	              .attr('class', 'process')
	  //	              .text(truncateName(name, 'process'))
	  //	              .style("text-anchor", "middle")
	  //	              .on("mouseover", scMouseOver)
	  //	              .on("mouseout", scMouseOut)
	  //	              .call(drag)
	  //
	  //	          g.append("text").attr("id", "text-" + gNum)
	  //	              .datum([{
	  //	                  cx: 0,
	  //	                  cy: 0
	  //                }])
	  //	              .attr('font-family', "FontAwesome, sans-serif")
	  //	              .attr('font-size', '0.9em')
	  //	              .attr("x", -6)
	  //	              .attr("y", 15)
	  //	              .text('\uf040')
	  //	              .on("mousedown", rename)
	  //
	  //	          //gnum(written in id): uniqe,
	  //	          g.append("text")
	  //	              .attr("id", "del-" + gNum)
	  //	              .attr('font-family', "FontAwesome, sans-serif")
	  //	              .attr('font-size', '1em')
	  //	              .attr("x", -6)
	  //	              .attr("y", r + ior / 2)
	  //	              .text('\uf014')
	  //	              .style("opacity", 0.2)
	  //	              .on("mousedown", removeElement)
	  //
	  //	          g.append("text")
	  //	              .attr("id", "info-" + gNum)
	  //	              .attr("class", "info-" + id)
	  //	              .attr('font-family', "FontAwesome, sans-serif")
	  //	              .attr('font-size', '1em')
	  //	              .attr("x", -6)
	  //	              .attr("y", -1 * (r + ior / 2 - 10))
	  //	              .text('\uf013')
	  //	              .style("opacity", 0.2)
	  //	              .on("mousedown", getInfo)
	  //
	  //	          // I/O id naming:[0]i = input,o = output -[1]process database ID -[2]The number of I/O of the selected process -[3]Parameter database ID- [4]uniqe number
	  //	          for (var k = 0; k < inputs.length; k++) {
	  //	              d3.select("#g-" + gNum).append("circle")
	  //	                  .attr("id", "i-" + (id) + "-" + k + "-" + inputs[k].parameter_id + "-" + gNum)
	  //	                  .attr("type", "I/O")
	  //	                  .attr("kind", "input")
	  //	                  .attr("parentG", "g-" + gNum)
	  //	                  .attr("name", inputs[k].name)
	  //	                  .attr("status", "standard")
	  //	                  .attr("connect", "single")
	  //	                  .attr("class", findType(inputs[k].parameter_id) + " input")
	  //	                  .attr("cx", calculatePos(inputs.length, k, "cx", "inputs"))
	  //	                  .attr("cy", calculatePos(inputs.length, k, "cy", "inputs"))
	  //	                  .attr("r", ior)
	  //	                  .attr("fill", "tomato")
	  //	                  .attr('fill-opacity', 0.8)
	  //	                  .on("mouseover", IOmouseOver)
	  //	                  .on("mousemove", IOmouseMove)
	  //	                  .on("mouseout", IOmouseOut)
	  //	                  .on("mousedown", IOconnect)
	  //	          }
	  //	          for (var k = 0; k < outputs.length; k++) {
	  //	              d3.select("#g-" + gNum).append("circle")
	  //	                  .attr("id", "o-" + (id) + "-" + k + "-" + outputs[k].parameter_id + "-" + gNum)
	  //	                  .attr("type", "I/O")
	  //	                  .attr("kind", "output")
	  //	                  .attr("parentG", "g-" + gNum)
	  //	                  .attr("name", outputs[k].name)
	  //	                  .attr("status", "standard")
	  //	                  .attr("connect", "single")
	  //	                  .attr("class", findType(outputs[k].parameter_id) + " output")
	  //	                  .attr("cx", calculatePos(outputs.length, k, "cx", "outputs"))
	  //	                  .attr("cy", calculatePos(outputs.length, k, "cy", "outputs"))
	  //	                  .attr("r", ior).attr("fill", "steelblue")
	  //	                  .attr('fill-opacity', 0.8)
	  //	                  .on("mouseover", IOmouseOver)
	  //	                  .on("mousemove", IOmouseMove)
	  //	                  .on("mouseout", IOmouseOut)
	  //	                  .on("mousedown", IOconnect)
	  //	          }
	  //	          processList[("g-" + gNum)] = name
	  //	          gNum = gNum + 1
	  //	      }
	  //
	  //	  }

	  function findType(id) {
	      parameter = parametersData.filter(function (el) {
	          return el.id == id
	      })
	      return parameter[0].file_type
	  }

	  function calculatePos(len, k, poz, type) {
	      degree = (180 / (len + 1)) * (k + 1)

	      inp = (270 - (180 / (len + 1)) * (k + 1)) * Math.PI / 180
	      out = (270 - (-180 / (len + 1)) * (k + 1)) * Math.PI / 180

	      if (type == "inputs") {
	          if (poz == "cx") {
	              calc = Math.cos(inp)
	              result = (calc * r)
	          } else {
	              calc = Math.sin(inp)
	              result = (calc * r)
	          }
	      } else {
	          if (poz == "cx") {
	              calc = Math.cos(out)
	              result = (calc * r)
	          } else {
	              calc = Math.sin(out)
	              result = (calc * r)
	          }
	      }
	      return result;
	  }

	  function mouseOverG() {
	      d3.select("#container").on("mousedown", null)
	      if (!binding) {
	          d3.select("#del-" + this.id.split("-")[1]).style("opacity", 1)
	          d3.select("#info-" + this.id.split("-")[1]).style("opacity", 1)
	      }
	  }

	  function mouseOutG() {
	      d3.select("#container").on("mousedown", cancel)
	      d3.select("#del-" + this.id.split("-")[1]).style("opacity", 0.2)
	      d3.select("#info-" + this.id.split("-")[1]).style("opacity", 0.2)

	  }

	  var drag = d3.behavior.drag()
	      .origin(function (d) {
	          return d;
	      })
	      .on("dragstart", dragstarted)
	      .on("drag", dragged)
	      .on("dragend", dragended);

	  function dragstarted(d) {

	      selectedg = document.getElementById(this.id).parentElement
	      coor = d3.mouse(this)
	      diffx = 0 - coor[0]
	      diffy = 0 - coor[1]
	      d3.event.sourceEvent.stopPropagation();
	      d3.select(document.getElementById(this.id).parentElement).classed("dragging", true);
	  }

	  function dragged(d) {
	      if (!binding) {
	          coor = d3.mouse(this)
	          t = d3.transform(d3.select('#' + document.getElementById(this.id).parentElement.id).attr("transform")),
	              x = t.translate[0]
	          y = t.translate[1]
	          d3.select(selectedg).attr("transform", "translate(" + (x + coor[0] + diffx) + "," + (y + coor[1] + diffy) + ")")
	          moveLine(selectedg.id, x, y, coor)
	      }
	  }

	  function dragended(d) {
	      d3.select(selectedg).classed("dragging", false);
	  }

	  function moveLine(gId, x, y, coor) {
	      allLines = d3.selectAll("line")[0]
	      for (var line = 0; line < allLines.length; line++) {
	          from = allLines[line].getAttribute("g_from")
	          to = allLines[line].getAttribute("g_to")

	          if (from == gId) {
	              lineid = allLines[line].id
	              IOid = lineid.split("_")[0]
	              IOx = d3.select("#" + IOid)[0][0].cx.baseVal.value
	              IOy = d3.select("#" + IOid)[0][0].cy.baseVal.value
	              d3.select("#" + lineid).attr("x1", coor[0] + diffx + IOx + x).attr("y1", coor[1] + diffy + IOy + y)
	              moveDelCircle(lineid)
	          } else if (to == gId) {
	              lineid = allLines[line].id
	              IOid = lineid.split("_")[1]
	              IOx = d3.select("#" + IOid)[0][0].cx.baseVal.value
	              IOy = d3.select("#" + IOid)[0][0].cy.baseVal.value
	              d3.select("#" + lineid).attr("x2", coor[0] + diffx + IOx + x).attr("y2", coor[1] + diffy + IOy + y)
	              moveDelCircle(lineid)
	          }
	      }
	  }

	  function moveDelCircle(lineid) {
	      x1 = d3.select("#" + lineid)[0][0].x1.baseVal.value
	      x2 = d3.select("#" + lineid)[0][0].x2.baseVal.value
	      y1 = d3.select("#" + lineid)[0][0].y1.baseVal.value
	      y2 = d3.select("#" + lineid)[0][0].y2.baseVal.value
	      d3.select("#c--" + lineid).attr("cx", (x1 + x2) / 2).attr("cy", (y1 + y2) / 2)
	      d3.select("#c--" + lineid).attr("transform", "translate(" + ((x1 + x2) / 2) + "," + ((y1 + y2) / 2) + ")")
	  }

	  function scMouseOver() {
	      parent = document.getElementById(this.id).parentElement.id;
	      if (this.id.split("-")[0] === "text") { //text üzerine gelince
	          cid = "sc-" + this.id.split("-")[1]
	      } else {
	          cid = this.id
	      }
	      d3.select("#" + cid).attr("fill", "gray")
	      if (!binding) {
	          d3.selectAll("line").attr("status", "hide")
	          d3.selectAll("line[g_from =" + parent + "]").attr("status", "standard")
	          d3.selectAll("line[g_to =" + parent + "]").attr("status", "standard")
	      }
	      showEdges()
	  }

	  function scMouseOut() {
	      if (this.id.split("-")[0] === "text") {
	          cid = "sc-" + this.id.split("-")[1]
	      } else {
	          cid = this.id
	      }
	      d3.select("#" + cid).attr("fill", "#BEBEBE")
	      if (!binding) {
	          d3.selectAll("line").attr("status", "standard")
	      }
	      showEdges()
	  }

	  function remove(delID) {
	      if (delID !== undefined) {
	          deleteID = delID;
	      }
	      if (!binding) {
	          g = document.getElementById(deleteID).parentElement.id //g-5

	          //--delete pipeline details
	          var gNum = g.split('-')[1];
	          var proClass = $('#' + g).attr('class') //
	          var proID = $('#' + g).attr('class').split('-')[1] //
	          if (proClass === 'g-inPro') { // input param is deleted
	              $('#inputTa-' + gNum).remove();
	          } else if (proClass === 'g-outPro') { // output param is deleted
	              $('#outputTa-' + gNum).remove();
	          } else { //process is deleted
	              removeProPipeTab(proID)
	          }
	          //--delete pipeline details ends

	          d3.select("#" + g).remove()
	          delete processList[g]
	          removeLines(g)
	      }
	  }

	  function removeLines(g) {

	      allLines = d3.selectAll("line")[0]
	      for (var line = 0; line < allLines.length; line++) {
	          from = allLines[line].getAttribute("g_from")
	          to = allLines[line].getAttribute("g_to")

	          if (from == g || to == g) {
	              lineid = allLines[line].id
	              removeEdge('c--' + lineid)
	          }
	      }
	  }

	  function removeDelCircle(lineid) {
	      d3.select("#c--" + lineid).remove()
	  }
	  var tooltip = d3.select("body")
	      .append("div").attr("class", "tooltip-svg")
	      .style("position", "absolute")
	      .style("max-width", "400px")
	      .style("max-height", "100px")
	      .style("opacity", .9)
	      .style("z-index", "10")
	      .style("visibility", "hidden")
	      .text("Something")
	      .style("color", "black");


	  function IOmouseOver() {
	      if (binding) {
	          if (d3.select("#" + this.id).attr("status") == "candidate") {
	              d3.select("#" + this.id).attr("status", "posCandidate")
	              showOptions()
	          }
	      } else {
	          className = document.getElementById(this.id).className.baseVal.split(" ")
	          cand = searchedType(className[1])
	          parentg = d3.select("#" + this.id).attr("parentG")
	          givenNamePP = document.getElementById(this.id).getAttribute("name")


	          //	          d3.selectAll("circle[type ='I/O']").attr("status", "noncandidate") //I/O olanları noncandia
	          if (className[0] === "connect_to_input") {
	              conToInput()
	              tooltip.html('Connect to input')
	          } else if (className[0] === "connect_to_output") {
	              conToOutput()
	              tooltip.html('Connect to output')
	          } else if (givenNamePP === 'inputparam') {
	              //	              d3.selectAll("." + className[0]).filter("." + cand).attr("status", "candidate")
	              var paraID = document.getElementById(this.id).id.split("-")[3]
	              var paraData = parametersData.filter(function (el) {
	                  return el.id == paraID
	              })
	              var paraFileType = paraData[0].file_type
	              tooltip.html('Input parameter<br/>File Type: <em>' + paraFileType + '</em>')
	          } else if (givenNamePP === 'outputparam') {
	              //Since outputparam is connected, it is not allowed to connect more parameters
	              //              d3.selectAll("." + className[0]).filter("." + cand).attr("status", "candidate")
	              var paraID = document.getElementById(this.id).id.split("-")[3]
	              var paraData = parametersData.filter(function (el) {
	                  return el.id == paraID
	              })
	              var paraFileType = paraData[0].file_type
	              tooltip.html('Output parameter<br/>File Type: <em>' + paraFileType + '</em>')
	          } else {
	              //	              d3.selectAll("." + className[0]).filter("." + cand).attr("status", "candidate")
	              var givenNamePP = document.getElementById(this.id).getAttribute("name")
	              var paraID = document.getElementById(this.id).id.split("-")[3]
	              var paraData = parametersData.filter(function (el) {
	                  return el.id == paraID
	              })
	              var paraFileType = paraData[0].file_type
	              var paraQualifier = paraData[0].qualifier
	              var paraName = paraData[0].name
	              tooltip.html('Identifier: <em>' + paraName + '</em><br/>Name: <em>' + givenNamePP + '</em><br/>File Type: <em>' + paraFileType + '</em><br/>Qualifier: <em>' + paraQualifier + '</em>')
	          }
	          //	          d3.selectAll("circle[parentG =" + parentg + "]").attr("status", "noncandidate")
	          d3.selectAll("#" + this.id).attr("status", "mouseon")
	          tooltip.style("visibility", "visible");


	          d3.selectAll("line").attr("status", "hide")
	          d3.selectAll("line[IO_from =" + this.id + "]").attr("status", "standard")
	          d3.selectAll("line[IO_to =" + this.id + "]").attr("status", "standard")

	          showOptions()
	          showEdges()
	      }
	  }

	  function IOmouseMove() {
	      tooltip.style("top", (event.pageY - 10) + "px").style("left", (event.pageX + 10) + "px");
	  }

	  function IOmouseOut() {
	      if (binding) {
	          if (d3.select("#" + this.id).attr("status") == "posCandidate") {
	              d3.select("#" + this.id).attr("status", "candidate")
	              showOptions()
	          }

	      } else {
	          d3.selectAll("circle[type ='I/O']").attr("status", "standard")
	          d3.selectAll("line").attr("status", "standard")
	          showOptions()
	          showEdges()
	      }
	      tooltip.style("visibility", "hidden");

	  }

	  function IOconnect() {
	      selectedIO = this.id //first click
	      className = document.getElementById(selectedIO).className.baseVal.split(" ")
	      cand = searchedType(className[1])
	      var givenNamePP = document.getElementById(this.id).getAttribute("name")
	      if (givenNamePP === 'outputparam' && className[0] !== 'connect_to_output') {
	          //If output parameter already connected , do nothing
	      } else {
	          if (binding) {
	              stopBinding(className, cand, selectedIO)
	          } else {
	              startBinding(className, cand, selectedIO)
	          }

	      }



	  }

	  function conToInput() {
	      d3.selectAll("circle").filter("." + cand).attr("status", "candidate") //select all available inputs for inputparam circles
	  }

	  function conToOutput() {
	      d3.selectAll("circle").filter("." + cand).attr("status", "candidate") //select all available outputs for outputparam circles
	  }

	  function startBinding(clasNames, cand, selectedIO) {
	      parentg = d3.select("#" + selectedIO).attr("parentG")

	      d3.selectAll("circle[type ='I/O']").attr("status", "noncandidate")

	      if (className[0] === "connect_to_input") {
	          conToInput()
	      } else if (className[0] === "connect_to_output") {
	          conToOutput()
	      } else {
	          d3.selectAll("." + className[0]).filter("." + cand).attr("status", "candidate")
	      }

	      d3.selectAll("circle[parentG =" + parentg + "]").attr("status", "noncandidate")
	      d3.selectAll("#" + selectedIO).attr("status", "selected")
	      d3.selectAll("line").attr("status", "hide")
	      d3.select("#del-" + selectedIO.split("-")[4]).style("opacity", 0.2)

	      for (var edge = 0; edge < edges.length; edge++) {
	          if (edges[edge].indexOf(selectedIO) > -1) {
	              d3.select("#" + findEdges(edges[edge], selectedIO)).attr("status", "noncandidate")
	          }
	      }
	      addCandidates2Dict()
	      binding = true
	      showOptions()
	      showEdges()
	  }

	  //second click selectedIO
	  function stopBinding() {
	      firstid = d3.select("circle[status ='selected']")[0][0].id
	      d3.selectAll("line").attr("status", "standard")
	      if (selectedIO === firstid) {
	          firstid = d3.select("#" + firstid).attr("status", "mouseon")
	          d3.selectAll("." + className[0]).filter("." + cand).attr("status", "candidate")
	          d3.select("#del-" + selectedIO.split("-")[4]).style("opacity", 1)
	      } else {
	          secondid = d3.select("circle[status ='posCandidate']")[0][0].id
	          createEdges(firstid, secondid)

	          d3.selectAll("circle[type ='I/O']").attr("status", "standard")
	          d3.select("#del-" + secondid.split("-")[4]).style("opacity", 1)
	      }
	      binding = false
	      showOptions()
	      showEdges()
	  }

	  function showOptions() {
	      d3.selectAll("circle[status ='standard']").attr("r", ior).style("stroke", "").style("stroke-width", "").style("stroke-opacity", "")
	      d3.selectAll("circle[status ='mouseon']").attr("r", ior * 1.4).style("stroke", "#ff9999").style("stroke-width", 4).style("stroke-opacity", .5)
	      d3.selectAll("circle[status ='selected']").attr("r", ior * 1.4).style("stroke", "#ff0000").style("stroke-width", 4).style("stroke-opacity", .5)
	      d3.selectAll("circle[status ='noncandidate']").attr("r", ior * 0.5).style("stroke", "")
	      d3.selectAll("circle[status ='candidate']").attr("r", ior * 1.4).style("stroke", "#ccff66").style("stroke-width", 4).style("stroke-opacity", .5)
	      d3.selectAll("circle[status ='posCandidate']").attr("r", ior * 1.4).style("stroke", "#ff9999").style("stroke-width", 4).style("stroke-opacity", .5)
	  }
	  var link = d3.svg.diagonal()
	      .projection(function (d) {
	          return [d.y, d.x];
	      });

	  function showEdges() {
	      d3.selectAll("line[status = 'standard']").style("stroke", "#B0B0B0").style("stroke-width", 4).attr("opacity", 1);
	      d3.selectAll("line[status = 'hide']").style("stroke-width", 2).attr("opacity", 0.3)
	  }

	  function searchedType(type) {
	      if (type == "input") {
	          return "output"
	      } else {
	          return "input"
	      }
	  }

	  function findEdges(edge, selectedIO) {
	      edgeNodes = edge.split("_")
	      if (edgeNodes[0] == selectedIO) {
	          return edgeNodes[1]
	      } else {
	          return edgeNodes[0]
	      }
	  }

	  function addCandidates2Dict() {
	      candidates = []
	      candList = d3.selectAll(("circle[status ='candidate']"))[0]
	      sel = d3.selectAll(("circle[status ='selected']"))[0][0]
	      candList.push(sel)

	      for (var c = 0; c < candList.length; c++) {
	          currid = candList[c].id
	          gid = document.getElementById(currid).parentElement.id;

	          t = d3.transform(d3.select('#' + gid).attr("transform")),
	              x = t.translate[0]
	          y = t.translate[1]

	          circx = candList[c].cx.baseVal.value + x
	          circy = candList[c].cy.baseVal.value + y

	          posList = [circx, circy, gid]

	          candidates[currid] = posList
	      }
	  }

	  function updateSecClassName(second, inputParamLocF) {
	      if (inputParamLocF === 0) {
	          var candi = "output"
	      } else {
	          var candi = "input"
	      }
	      secClassName = document.getElementById(second).className.baseVal.split("-")[0].split(" ")[0] + " " + candi
	      return secClassName
	  }

	  function createEdges(first, second) {
	      d3.selectAll("#" + first).attr("connect", 'mate')
	      d3.selectAll("#" + second).attr("connect", 'mate')
	      inputParamLocF = first.indexOf("o-inPro") //-1: inputparam not exist //0: first click is done on the inputparam
	      inputParamLocS = second.indexOf("o-inPro")
	      outputParamLocF = first.indexOf("i-outPro") //-1: outputparam not exist //0: first click is done on the inputparam
	      outputParamLocS = second.indexOf("i-outPro")


	      if (inputParamLocS === 0 || outputParamLocS === 0) { //second click is done on the circle of inputparam//outputparam
	          //swap elements and treat as fırst click was done on
	          tem = second
	          second = first
	          first = tem
	          inputParamLocF = 0
	          outputParamLocF = 0
	      }
	      //first click is done on the circle of inputparam
	      if (inputParamLocF === 0 || outputParamLocF === 0) {

	          //update the class of inputparam based on selected second circle  
	          secClassName = updateSecClassName(second, inputParamLocF)
	          d3.selectAll("#" + first).attr("class", secClassName)
	          //update the parameter of the inputparam based on selected second circle
	          var firGnum = document.getElementById(first).id.split("-")[4] //first g-number
	          var secGnum = document.getElementById(second).id.split("-")[4] //first g-number
	          secPI = document.getElementById(second).id.split("-")[3] //second parameter id
	          var secProI = document.getElementById(second).id.split("-")[1] //second process id
	          patt = /(.*)-(.*)-(.*)-(.*)-(.*)/
	          secID = first.replace(patt, '$1-$2-$3-' + secPI + '-$5')

	          d3.selectAll("#" + first).attr("id", secID)
	          fClickOrigin = first
	          fClick = secID
	          sClick = second
	          var rowType = '';
	          //Pipeline details table 
	          if (inputParamLocF === 0) {
	              rowType = 'input';
	          } else if (outputParamLocF === 0) {
	              rowType = 'output';
	          }
	          var paramGivenName = document.getElementById('text-' + firGnum).getAttribute("name");
	          var paraData = parametersData.filter(function (el) { return el.id == secPI });
	          var procData = processData.filter(function (el) { return el.id == secProI });
	          var paraFileType = paraData[0].file_type;
	          var paraQualifier = paraData[0].qualifier;
	          var paraIdentifier = paraData[0].name;
	          //var processName = procData[0].name;
	          var processName = $('#text-' + secGnum).attr('name');

	          //var givenNamePP = document.getElementById(second).getAttribute("name")
	          var rowExist = ''
	          rowExist = document.getElementById(rowType + 'Ta-' + firGnum);
	          if (rowExist) {
	              var preProcess = '';
	              $('#' + rowType + 'Ta-' + firGnum + '> :nth-child(5)').append('<span id=proGcomma-' + secGnum + '>, </span>');
	              $('#' + rowType + 'Ta-' + firGnum + '> :nth-child(5)').append('<span id=proGName-' + secGnum + '>' + processName + '</span>');
	          } else {
	              if (rowType === 'input') {
	                  if (paraQualifier === 'file') {
	                      var selectFileButton = getButtonsModal('inputFile', 'Select File');
	                  } else if (paraQualifier === 'val') {
	                      var selectFileButton = getButtonsModal('inputVal', 'Enter Value');
	                  } else {
	                      var selectFileButton = getButtonsModal('inputFile', 'Select Set');
	                  }
	                  var inRow = insertRowTable(rowType, firGnum, secGnum, paramGivenName, paraIdentifier, paraFileType, paraQualifier, processName, selectFileButton);
	                  $('#' + rowType + 'sTable > tbody:last-child').append(inRow);
	                  //xxx get project_pipeline_inputs:
	                  var getProPipeInputs = getValues({
	                      p: "getProjectPipelineInputs",
	                      project_pipeline_id: project_pipeline_id,
	                      g_num: firGnum
	                  });
	                  if (getProPipeInputs.length === 1) {
	                      var rowID = rowType + 'Ta-' + firGnum;
	                      var filePath = getProPipeInputs[0].name; //value for val type
	                      var proPipeInputID = getProPipeInputs[0].id;
	                      insertSelectInput(rowID, firGnum, filePath, proPipeInputID, paraQualifier);

	                  }
	              } else if (rowType === 'output') {
	                  var inRow = insertRowTable(rowType, firGnum, secGnum, paramGivenName, paraIdentifier, paraFileType, paraQualifier, processName, 'NA');
	                  $('#' + rowType + 'sTable > tbody:last-child').append(inRow);
	              }

	          }

	      } else { //process to process connection
	          fClickOrigin = first
	          fClick = first
	          sClick = second
	      }

	      d3.select("#mainG").append("line")
	          .attr("id", fClick + "_" + sClick)
	          .attr("class", "line")
	          .attr("type", "standard")
	          .style("stroke", "#B0B0B0").style("stroke-width", 4)
	          .attr("x1", candidates[fClickOrigin][0])
	          .attr("y1", candidates[fClickOrigin][1])
	          .attr("x2", candidates[sClick][0])
	          .attr("y2", candidates[sClick][1])
	          .attr("g_from", candidates[fClickOrigin][2])
	          .attr("g_to", candidates[sClick][2])
	          .attr("IO_from", fClick)
	          .attr("IO_to", sClick)
	          .attr("stroke-width", 2)
	          .attr("stroke", "black")

	      edges.push(fClick + "_" + sClick)

	  }

	  function removeEdge(delID) {
	      if (delID !== undefined) {
	          deleteID = delID;
	      }

	      d3.select("#" + deleteID).remove() //eg. c--o-inPro-1-9-0_i-10-0-9-1
	      d3.select("#" + deleteID.split("--")[1]).remove()
	      edges.splice(edges.indexOf(deleteID.split("--")[1]), 1);
	      var firstParamId = deleteID.split("--")[1].split("_")[0];
	      var secondParamId = deleteID.split("--")[1].split("_")[1];
	      var paramType = firstParamId.split("-")[1] //inPro or outPro
	      var delsecGnum = secondParamId.split("-")[4] //gNum
	      var delGnum = firstParamId.split("-")[4] //gNum

	      //input/output param has still edge/edges
	      //remove process name from pipeline details table
	      if (edges.searchFor(firstParamId)) {
	          if (paramType === 'inPro') {
	              //$('#inputTa-' + delGnum + '> :last-child').append('<span id=proGName-' + secGnum + '>' + processName + '</span>');
	              $('#inputTa-' + delGnum + '> :last-child > ' + '#proGName-' + delsecGnum).remove();
	              if ($('#inputTa-' + delGnum + '> :last-child > ' + '#proGcomma-' + delsecGnum)[0]) {
	                  $('#inputTa-' + delGnum + '> :last-child > ' + '#proGcomma-' + delsecGnum).remove();
	              } else {
	                  $('#inputTa-' + delGnum + '> :last-child > :first-child').remove();
	              }

	          }
	      }

	      //input/output param has no edge any more
	      if (!edges.searchFor(firstParamId)) {
	          d3.selectAll("#" + firstParamId).attr("connect", 'single')
	          //remove row from pipeline details table
	          if (paramType === 'inPro') {
	              $('#inputTa-' + delGnum).remove() //gNum
	          } else if (paramType === 'outPro') {
	              $('#outputTa-' + delGnum).remove() //gNum
	          }
	      }
	      //process has no edge any more
	      if (!edges.searchFor(secondParamId)) {
	          d3.selectAll("#" + secondParamId).attr("connect", 'single')
	      }
	  }

	  function delMouseOver() {
	      d3.select("#del" + this.id).attr('fill-opacity', 0.8)
	      d3.select("#del--" + this.id.split("--")[1]).style("opacity", 0.8)
	  }

	  function delMouseOut() {
	      d3.select("#del" + this.id).attr('fill-opacity', 0.4)
	      d3.select("#del--" + this.id.split("--")[1]).style("opacity", 0.4)
	  }

	  function cancel() {
	      if (binding) {
	          d3.selectAll("circle[type ='I/O']").attr("status", "standard")
	          binding = false
	          showOptions()
	      }
	  }

	  function rename() {
	      renameTextID = this.id;
	      renameText = d3.select("#" + this.id).attr('name');
	      body = document.body;
	      bodyW = body.offsetWidth;
	      bodyH = body.scrollHeight;
	      $('#renameModal').modal("show");
	  }

	  function changeName() {
	      newName = document.getElementById("mRenName").value
	      d3.select("#" + renameTextID).attr('name', newName)
	      newNameShow = truncateName(newName, d3.select("#" + renameTextID).attr('class'));
	      d3.select("#" + renameTextID).text(newNameShow)

	      //update pipeline details table
	      proType = $('#' + renameTextID).parent().attr('class').split('-')[1];
	      var gNumP = renameTextID.split('-')[1];
	      $('span[id="proGName-' + gNumP + '\"]').text(newName);
	      if (proType === 'inPro') {
	          $('#input-PName-' + renameTextID.split('-')[1]).text(newName); //id=input-PName-0
	      } else if (proType === 'outPro') {
	          $('#output-PName-' + renameTextID.split('-')[1]).text(newName); //id=output-PName-0
	      }
	      processList[document.getElementById(renameTextID).parentElement.id] = newName
	      document.getElementById(renameTextID).parentElement.id
	  }


	  function getInfo() {
	      className = document.getElementById(this.id).className.baseVal.split("-");
	      gNumInfo = this.id.split("-")[1];
	      infoID = className[1];
	      $('#addProcessModal').modal("show");
	  }

	  function removeElement(delID) {
	      if (delID !== undefined) {
	          deleteID = delID;
	      } else {
	          deleteID = this.id;
	      }
	      body = document.body
	      bodyW = body.offsetWidth
	      bodyH = body.offsetHeight

	      if (!binding) {
	          $('#confirmD3Modal').modal("show");
	      }
	  }



	  function download(text) {
	      var filename = $('#pipeline-title').val() + '.nf';
	      var element = document.createElement('a');
	      element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
	      element.setAttribute('download', filename);

	      element.style.display = 'none';
	      document.body.appendChild(element);

	      element.click();

	      document.body.removeChild(element);
	  }

	  //	  function resetPos() {
	  //	      d3.select("#mainG").attr("transform", "translate(0,0)scale(1)")
	  //	  }



	  //	  function save() {
	  //	      saveNodes = {}
	  //	      saveMainG = {}
	  //	      for (var key in processList) {
	  //	          t = d3.transform(d3.select('#' + key).attr("transform")),
	  //	              x = t.translate[0]
	  //	          y = t.translate[1]
	  //	          gClass = document.getElementById(key).className.baseVal
	  //	          prosessID = gClass.split("-")[1]
	  //	          processName = processList[key]
	  //	          saveNodes[key] = [x, y, prosessID, processName]
	  //	      }
	  //	      Maint = d3.transform(d3.select('#' + "mainG").attr("transform")),
	  //	          Mainx = Maint.translate[0]
	  //	      Mainy = Maint.translate[1]
	  //	      Mainz = Maint.scale[0]
	  //	      sName = document.getElementById("pipeline-title").value;
	  //	      var pipelineSummary = $('#pipelineSum').val();
	  //	      id = 0
	  //	      if (sName !== "" && dupliPipe === false) {
	  //	          id = $("#pipeline-title").attr('pipelineid');
	  //	      } else if (sName !== "" && dupliPipe === true) {
	  //	          id = '';
	  //	          sName = sName + '-copy'
	  //	      }
	  //
	  //	      saveMainG["mainG"] = [Mainx, Mainy, Mainz]
	  //	      savedList = [{
	  //	          "name": sName
	  //	      }, {
	  //	          "id": id
	  //	      }, {
	  //	          "nodes": saveNodes
	  //	      }, saveMainG, {
	  //	          "edges": edges
	  //	      }, {
	  //	          "summary": pipelineSummary
	  //	      }];
	  //
	  //	      //xxx
	  //	      //A. Add new pipeline
	  //	      if (sName !== "" && id === '') {
	  //	          var maxPipeline_gid = getValues({ p: "getMaxPipeline_gid" })[0].pipeline_gid;
	  //	          var newPipeline_gid = parseInt(maxPipeline_gid) + 1;
	  //	          savedList.push({ "pipeline_gid": newPipeline_gid });
	  //	          sl = JSON.stringify(savedList);
	  //	          var ret = getValues({ p: "saveAllPipeline", dat: sl });
	  //	          $("#pipeline-title").attr('pipelineid', ret.id);
	  //	          pipeline_id = $('#pipeline-title').attr('pipelineid'); //refresh pipeline_id
	  //	          $('#allPipelines').append('<li><a href="index.php?np=1&id=' + ret.id + '" class="pipelineItems" draggable="false" id="pipeline-' + ret.id + '"><i class="fa fa-angle-double-right"></i>' + sName + '</a></li>');
	  //	          if (dupliPipe === true) {
	  //	              $("#pipeline-title").changeVal(sName);
	  //	              dupliPipe = false;
	  //	          }
	  //	          $('#autosave').text('All changes saved');
	  //
	  //
	  //	      }
	  //	      //B. pipeline already exist
	  //	      else if (sName !== "" && id !== '') {
	  //	          var warnUserPipe = false;
	  //	          var warnPipeText = '';
	  //              [warnUserPipe, warnPipeText] = checkRevisionPipe(id);
	  //	          //B.1 allow updating on existing pipeline
	  //	          if (warnUserPipe === false) {
	  //	              sl = JSON.stringify(savedList);
	  //	              var ret = getValues({ p: "saveAllPipeline", dat: sl });
	  //	              pipeline_id = $('#pipeline-title').attr('pipelineid'); //refresh pipeline_id
	  //	              refreshCreatorData(pipeline_id);
	  //	              document.getElementById('pipeline-' + pipeline_id).innerHTML = '<i class="fa fa-angle-double-right"></i>' + sName;
	  //	              $('#autosave').text('All changes saved');
	  //
	  //
	  //	          }
	  //	          //B.2 allow save on new revision
	  //	          else if (warnUserPipe === true) {
	  //	              // ConfirmYesNo process modal 
	  //	              $('#confirmRevision').off();
	  //	              $('#confirmRevision').on('show.bs.modal', function (event) {
	  //	                  $(this).find('form').trigger('reset');
	  //	                  $('#confirmYesNoText').html(warnPipeText);
	  //	              });
	  //
	  //	              $('#confirmRevision').on('click', '.cancelRev', function (event) {
	  //	                  $('#autosave').text('Changes not saved!');
	  //
	  //	              });
	  //
	  //	              $('#confirmRevision').on('click', '#saveRev', function (event) {
	  //	                  var confirmformValues = $('#confirmRevision').find('input');
	  //	                  var revCommentData = confirmformValues.serializeArray();
	  //	                  var revComment = revCommentData[0].value;
	  //	                  if (revComment === '') { //xxx warn user to enter comment
	  //	                  } else if (revComment !== '') {
	  //	                      var pipeline_gid = getValues({ p: "getPipeline_gid", "pipeline_id": id })[0].pipeline_gid;
	  //	                      var maxPipRev_id = getValues({ p: "getMaxPipRev_id", "pipeline_gid": pipeline_gid })[0].rev_id;
	  //	                      var newPipRev_id = parseInt(maxPipRev_id) + 1;
	  //	                      savedList[1].id = ''
	  //	                      savedList.push({ "pipeline_gid": pipeline_gid });
	  //	                      savedList.push({ "rev_comment": revComment });
	  //	                      savedList.push({ "rev_id": newPipRev_id });
	  //	                      sl = JSON.stringify(savedList);
	  //	                      var ret = getValues({ p: "saveAllPipeline", dat: sl });
	  //	                      $('#confirmRevision').modal('hide');
	  //	                      $('#autosave').text('Changes saved on new revision');
	  //	                      setTimeout(function () { window.location.replace("index.php?np=1&id=" + ret.id); }, 700);
	  //	                  }
	  //	              });
	  //	              $('#confirmRevision').modal('show');
	  //	              pipeline_id = $('#pipeline-title').attr('pipelineid'); //refresh pipeline_id
	  //	              refreshCreatorData(pipeline_id);
	  //	          }
	  //	      }
	  //
	  //	  }

	  function loadPipeline(sDataX, sDataY, sDatapId, sDataName, gN) {
	      t = d3.transform(d3.select('#' + "mainG").attr("transform")),
	          x = t.translate[0]
	      y = t.translate[1]
	      z = t.scale[0]

	      gNum = parseInt(gN)
	      var name = sDataName
	      var name = sDataName
	      var id = sDatapId
	      var process_id = id

	      //for input parameters
	      if (id === "inPro") {
	          ipR = 70 / 2
	          ipIor = ipR / 3
	          var kind = "input"

	          //(A)if edges are not formed parameter_id data comes from default: process_parameter table "name" column
	          var paramId = "inPara" //default
	          var classtoparam = "connect_to_input output"
	          var pName = "inputparam"
	          var init = "o"
	          var pColor = "orange"
	          //(B)if edges are formed parameter_id data comes from biocorepipesave table "edges" column
	          edgeIn = sData[0].edges
	          edgeInP = JSON.parse(edgeIn.replace(/'/gi, "\""))["edges"] //i-10-0-9-1_o-inPro-1-9-0

	          for (var ee = 0; ee < edgeInP.length; ee++) {
	              patt = /(.*)-(.*)-(.*)-(.*)-(.*)_(.*)-(.*)-(.*)-(.*)-(.*)/
	              edgeFirstPId = edgeInP[ee].replace(patt, '$2')
	              edgeFirstGnum = edgeInP[ee].replace(patt, '$5')
	              edgeSecondParID = edgeInP[ee].replace(patt, '$9')

	              if (edgeFirstGnum === String(gNum) && edgeFirstPId === "inPro") {
	                  paramId = edgeSecondParID //if edge is found
	                  classtoparam = findType(paramId) + " output"
	                  pName = parametersData.filter(function (el) {
	                      return el.id == paramId
	                  })[0].name
	                  break
	              }
	          }

	          drawParam(name, process_id, id, kind, sDataX, sDataY, paramId, pName, classtoparam, init, pColor)
	          processList[("g-" + gNum)] = name
	          gNum = gNum + 1


	      } else if (id === "outPro") {
	          ipR = 70 / 2
	          ipIor = ipR / 3
	          var kind = "output"

	          //(A)if edges are not formed parameter_id data comes from default: process_parameter table "name" column
	          var paramId = "outPara" //default
	          var classtoparam = "connect_to_output input"
	          var pName = "outputparam"
	          var init = "i"
	          var pColor = "green"
	          //(B)if edges are formed parameter_id data comes from biocorepipesave table "edges" column
	          edgeOut = sData[0].edges
	          edgeOutP = JSON.parse(edgeOut.replace(/'/gi, "\""))["edges"] //i-10-0-9-1_o-inPro-1-9-0

	          for (var ee = 0; ee < edgeOutP.length; ee++) {
	              patt = /(.*)-(.*)-(.*)-(.*)-(.*)_(.*)-(.*)-(.*)-(.*)-(.*)/
	              edgeFirstPId = edgeOutP[ee].replace(patt, '$2')
	              edgeFirstGnum = edgeOutP[ee].replace(patt, '$5')
	              edgeSecondParID = edgeOutP[ee].replace(patt, '$9')

	              if (edgeFirstGnum === String(gNum) && edgeFirstPId === "outPro") {
	                  paramId = edgeSecondParID //if edge is found
	                  classtoparam = findType(paramId) + " input"
	                  pName = parametersData.filter(function (el) {
	                      return el.id == paramId
	                  })[0].name
	                  break
	              }
	          }
	          drawParam(name, process_id, id, kind, sDataX, sDataY, paramId, pName, classtoparam, init, pColor)
	          processList[("g-" + gNum)] = name
	          gNum = gNum + 1

	      } else {
	          addProPipeTab(id, gNum)
	          //--Pipeline details table ends---

	          inputs = getValues({
	              p: "getInputsPP",
	              "process_id": id
	          })

	          outputs = getValues({
	              p: "getOutputsPP",
	              "process_id": id
	          })

	          //gnum uniqe, id same id (Written in class) in same type process
	          g = d3.select("#mainG").append("g")
	              .attr("id", "g-" + gNum)
	              .attr("class", "g-" + id)
	              .attr("transform", "translate(" + (sDataX) + "," + (sDataY) + ")")

	          //gnum(written in id): uniqe, id(Written in class): same id in same type process, bc(written in type): same at all bc
	          g.append("circle").attr("id", "bc-" + gNum)
	              .attr("class", "bc-" + id)
	              .attr("type", "bc")
	              .attr("cx", cx)
	              .attr("cy", cy)
	              .attr("r", r + ior)
	              .attr('fill-opacity', 0.6)
	              .attr("fill", "red")
	              .transition()
	              .delay(500)
	              .duration(3000)
	              .attr("fill", "#E0E0E0")
	          //gnum(written in id): uniqe, id(Written in class): same id in same type process, sc(written in type): same at all bc
	          g.append("circle")
	              .datum([{
	                  cx: 0,
	                  cy: 0
			        }])
	              .attr("id", "sc-" + gNum)
	              .attr("class", "sc-" + id)
	              .attr("type", "sc")
	              .attr("r", r - ior)
	              .attr("fill", "#BEBEBE")
	              .attr('fill-opacity', 0.6)

	          //gnum(written in id): uniqe,
	          g.append("text").attr("id", "text-" + gNum)
	              .datum([{
	                  cx: 0,
	                  cy: 0
			        }])
	              .attr('font-family', "FontAwesome, sans-serif")
	              .attr('font-size', '1em')
	              .attr('name', name)
	              .attr('class', 'process')
	              .text(truncateName(name, 'process'))
	              .style("text-anchor", "middle")

	          // I/O id naming:[0]i = input,o = output -[1]process database ID -[2]The number of I/O of the selected process -[3]Parameter database ID- [4]uniqe number
	          for (var k = 0; k < inputs.length; k++) {
	              d3.select("#g-" + gNum).append("circle")
	                  .attr("id", "i-" + (id) + "-" + k + "-" + inputs[k].parameter_id + "-" + gNum)
	                  .attr("type", "I/O")
	                  .attr("kind", "input")
	                  .attr("parentG", "g-" + gNum)
	                  .attr("name", inputs[k].name)
	                  .attr("connect", "single")
	                  .attr("status", "standard")
	                  .attr("class", findType(inputs[k].parameter_id) + " input")
	                  .attr("cx", calculatePos(inputs.length, k, "cx", "inputs"))
	                  .attr("cy", calculatePos(inputs.length, k, "cy", "inputs"))
	                  .attr("r", ior)
	                  .attr("fill", "tomato")
	                  .attr('fill-opacity', 0.8)
	                  .on("mouseover", IOmouseOver)
	                  .on("mousemove", IOmouseMove)
	                  .on("mouseout", IOmouseOut)
	              //	                  .on("mousedown", IOconnect)
	          }

	          for (var k = 0; k < outputs.length; k++) {
	              d3.select("#g-" + gNum).append("circle")
	                  .attr("id", "o-" + (id) + "-" + k + "-" + outputs[k].parameter_id + "-" + gNum)
	                  .attr("type", "I/O")
	                  .attr("kind", "output")
	                  .attr("parentG", "g-" + gNum)
	                  .attr("name", outputs[k].name)
	                  .attr("status", "standard")
	                  .attr("connect", "single")
	                  .attr("class", findType(outputs[k].parameter_id) + " output")
	                  .attr("cx", calculatePos(outputs.length, k, "cx", "outputs"))
	                  .attr("cy", calculatePos(outputs.length, k, "cy", "outputs"))
	                  .attr("r", ior).attr("fill", "steelblue")
	                  .attr('fill-opacity', 0.8)
	                  .on("mouseover", IOmouseOver)
	                  .on("mousemove", IOmouseMove)
	                  .on("mouseout", IOmouseOut)
	              //	                  .on("mousedown", IOconnect)
	          }
	          processList[("g-" + gNum)] = name
	          gNum = gNum + 1
	      }
	  }

	  function addCandidates2DictForLoad(fir) {
	      candidates = []
	      candList = d3.selectAll(("circle[type ='I/O']"))[0]
	      sel = d3.select(("#" + fir))[0][0]
	      candList.push(sel)
	      for (var c = 0; c < candList.length; c++) {
	          currid = candList[c].id
	          gid = document.getElementById(currid).parentElement.id;

	          t = d3.transform(d3.select('#' + gid).attr("transform")),
	              x = t.translate[0]
	          y = t.translate[1]

	          circx = candList[c].cx.baseVal.value + x
	          circy = candList[c].cy.baseVal.value + y

	          posList = [circx, circy, gid]
	          candidates[currid] = posList
	      }
	  }

	  function saveReady() {
	      document.getElementById("savePipeline").disabled = false;
	  }


	  function loadPipelineDetails(pipeline_id) {
	      var getPipelineD = [];
	      getPipelineD.push({ name: "id", value: pipeline_id });
	      getPipelineD.push({ name: "p", value: 'loadPipeline' });
	      $.ajax({
	          type: "POST",
	          url: "ajax/ajaxquery.php",
	          data: getPipelineD,
	          async: true,
	          success: function (s) {
	              $('#pipeline-title').text(s[0].name);
	              $('#pipeline-title').attr('href', 'index.php?np=1&id=' + pipeline_id);
	              $('#project-title').attr('href', 'index.php?np=2&id=' + project_id);
	              $('#pipelineSum').val(s[0].summary);
	              openPipeline(pipeline_id);
	              $('#pipelineSum').attr('disabled', "disabled");
	          },
	          error: function (errorThrown) {
	              alert("Error: " + errorThrown);
	          }
	      });
	  };

	  function updateCheckBox(check_id, status) {
	      if (check_id === '#exec_all' && status === "false") {
	          $(check_id).trigger("click");
	      } else if ((check_id === '#exec_each' || check_id === '#singu_check' || check_id === '#docker_check') && status === "true") {
	          $(check_id).trigger("click");
	      }
	      if (status === "true") {
	          $(check_id).attr('checked', true);
	      } else if (status === "false") {
	          $(check_id).removeAttr('checked');
	      }
	  }

	  function refreshCreatorData(project_pipeline_id) {
	      pipeData = getValues({ p: "getProjectPipelines", id: project_pipeline_id });
	      console.log(pipeData);
	      if (pipeData) {
	          $('#creatorInfoPip').css('display', "block");
	          $('#ownUserNamePip').text(pipeData[0].username);
	          $('#datecreatedPip').text(pipeData[0].date_created);
	          $('.lasteditedPip').text(pipeData[0].date_modified);
	      }
	  }

	  function IsJsonString(str) {
	      try {
	          JSON.parse(str);
	      } catch (e) {
	          return false;
	      }
	      return true;
	  }

	  function loadProjectPipeline(pipeData) {
	      loadRunOptions();
	      $('#creatorInfoPip').css('display', "block");
	      $('#project-title').text(pipeData[0].project_name);
	      $('#run-title').changeVal(pipeData[0].pp_name);
	      $('#runSum').val(pipeData[0].summary);
	      $('#rOut_dir').val(pipeData[0].output_dir);
	      $('#chooseEnv').val(pipeData[0].profile);
	      $('#perms').val(pipeData[0].perms);
	      $('#runCmd').val(pipeData[0].cmd);
	      updateCheckBox('#intermeDel', pipeData[0].interdel);
	      updateCheckBox('#exec_each', pipeData[0].exec_each);
	      updateCheckBox('#exec_all', pipeData[0].exec_all);
	      updateCheckBox('#docker_check', pipeData[0].docker_check);
	      updateCheckBox('#singu_check', pipeData[0].singu_check);
	      $('#docker_img').val(pipeData[0].docker_img);
	      $('#singu_img').val(pipeData[0].singu_img);

	      if (pipeData[0].group_id !== "0") {
	          $('#groupSel').val(pipeData[0].group_id);
	      }
	      //xxx

	      //insert exec_next_settings data into exec_next_settings table 
	      if (IsJsonString(pipeData[0].exec_next_settings)) {
	          var exec_next_settings = JSON.parse(pipeData[0].exec_next_settings);
	          fillForm('#execNextSettTable', 'input', exec_next_settings);
	      }

	      //insert exec_all_settings data into allProcessSettTable table 
	      if (IsJsonString(pipeData[0].exec_all_settings)) {
	          var exec_all_settings = JSON.parse(pipeData[0].exec_all_settings);
	          fillForm('#allProcessSettTable', 'input', exec_all_settings);
	      }

	      //	      var exec_each_settings 
	      console.log(pipeData)


	      $('#ownUserNamePip').text(pipeData[0].username);
	      $('#datecreatedPip').text(pipeData[0].date_created);
	      $('.lasteditedPip').text(pipeData[0].date_modified);
	  }

	  function loadRunOptions() {
	      //get profiles for user
	      var proLocData = getValues({ p: "getProfileLocal" });
	      var proCluData = getValues({ p: "getProfileCluster" });
	      if (proLocData.length + proCluData.length !== 0) {
	          $.each(proLocData, function (el) {
	              var option = new Option(proLocData[el].name + ' (local)', 'local-' + proLocData[el].id);
	              $("#chooseEnv").append(option);
	          });
	          $.each(proCluData, function (el) {
	              var option = new Option(proCluData[el].name + ' (cluster: ' + proCluData[el].username + '@' + proCluData[el].hostname + ')', 'cluster-' + proCluData[el].id);
	              $("#chooseEnv").append(option);
	          });
	      }
	  }



	  function insertSelectInput(rowID, gNumParam, filePath, proPipeInputID, qualifier) {
	      if (qualifier === 'file' || qualifier === 'set') {
	          var editIcon = getIconButtonModal('inputFile', 'Edit', 'fa fa-pencil');
	          var deleteIcon = getIconButton('inputDel', 'Delete', 'fa fa-trash-o');
	          $('#' + rowID).find('#inputFileSelect').css('display', 'none');
	      } else {
	          var editIcon = getIconButtonModal('inputVal', 'Edit', 'fa fa-pencil');
	          var deleteIcon = getIconButton('inputVal', 'Delete', 'fa fa-trash-o');
	          $('#' + rowID).find('#inputValEnter').css('display', 'none');
	      }
	      $('#' + rowID + '> :nth-child(6)').append('<span style="padding-right:7px;" id=filePath-' + gNumParam + '>' + filePath + '</span>' + editIcon + deleteIcon);
	      $('#' + rowID).attr('propipeinputid', proPipeInputID);
	  }
	  //xxx
	  function removeSelectFile(rowID, sType) {
	      if (sType === 'file' || sType === 'set') {
	          $('#' + rowID).find('#inputFileSelect').css('display', 'inline');
	      } else if (sType === 'val') {
	          $('#' + rowID).find('#inputValEnter').css('display', 'inline');
	      }
	      $('#' + rowID + '> :nth-child(6) > span').remove();
	      $('#' + rowID + '> :nth-child(6) > button')[2].remove();
	      $('#' + rowID + '> :nth-child(6) > button')[1].remove();
	      $('#' + rowID).removeAttr('propipeinputid');
	  }

	  function saveFileSetValModal(data, sType) {
	      if (sType === 'file' || sType === 'set') {
	          var rowID = $('#mIdFile').attr('rowID'); //the id of table-row to be updated #inputTa-3
	      } else if (sType === 'val') {
	          var rowID = $('#mIdVal').attr('rowID'); //the id of table-row to be updated #inputTa-3
	      }
	      var gNumParam = rowID.split('-')[1];
	      var given_name = $("#input-PName-" + gNumParam).text(); //input-PName-3
	      var qualifier = $('#' + rowID + ' > :nth-child(4)').text(); //input-PName-3
	      data.push({ name: "p", value: "saveInput" });
	      //insert into input table
	      var inputGet = getValues(data);
	      var input_id = inputGet.id;
	      //insert into project_input table
	      var proInputGet = getValues({ "p": "saveProjectInput", "input_id": input_id, "project_id": project_id });
	      var projectInputID = proInputGet.id;
	      //insert into project_pipeline_input table
	      var propipeInputGet = getValues({
	          "p": "saveProPipeInput",
	          "input_id": input_id,
	          "project_id": project_id,
	          "pipeline_id": pipeline_id,
	          "project_pipeline_id": project_pipeline_id,
	          "g_num": gNumParam,
	          "given_name": given_name,
	          "qualifier": qualifier
	      });
	      var projectPipelineInputID = propipeInputGet.id;
	      //get inputdata from input table
	      var proInputGet = getValues({ "p": "getInputs", "id": input_id, });
	      var filePath = proInputGet[0].name;
	      //insert into #inputsTab
	      insertSelectInput(rowID, gNumParam, filePath, projectPipelineInputID, sType);
	      checkReadytoRun();
	  }

	  function editFileSetValModal(data, sType) {
	      if (sType === 'file' || sType === 'set') {
	          var rowID = $('#mIdFile').attr('rowID'); //the id of table-row to be updated #inputTa-3
	      } else if (sType === 'val') {
	          var rowID = $('#mIdVal').attr('rowID'); //the id of table-row to be updated #inputTa-3
	      }
	      var gNumParam = rowID.split('-')[1];
	      var given_name = $("#input-PName-" + gNumParam).text(); //input-PName-3
	      data.push({ name: "p", value: "saveInput" });
	      //update file table
	      var inputGet = getValues(data);
	      var input_id = data[0].value;
	      //update #inputsTab
	      $('#filePath-' + gNumParam).text(data[1].value);
	      checkReadytoRun();
	  }

	  function checkReadytoRun() {
	      project_pipeline_id = $('#pipeline-title').attr('projectpipelineid');
	      var getProPipeInputs = getValues({
	          p: "getProjectPipelineInputs",
	          project_pipeline_id: project_pipeline_id,
	      });
	      var numInputRows = $('#inputsTable > tbody').find('tr').length;
	      var profileNext = $('#chooseEnv').find(":selected").val();
	      var output_dir = $('#rOut_dir').val();
	      if ($('#statusProPipe').css('display') === 'inline-block' || $('#runProPipe').css('display') === 'inline-block') {
	          if (getProPipeInputs.length === numInputRows && profileNext !== '' && output_dir !== '') {
	              $('#runProPipe').css('display', 'inline');
	              $('#statusProPipe').css('display', 'none');
	          } else {
	              $('#runProPipe').css('display', 'none');
	              $('#statusProPipe').css('display', 'inline');
	          }
	      }

	  }

//callbackfunction to first change the status of button to connecting
//function doSomething(callback) {
//    // ...
//    // Call the callback
//    callback('stuff', 'goes', 'here');
//}
//function foo(a, b, c) {
//    // I'm the callback
//    alert(a + " " + b + " " + c);
//}
//doSomething(foo);


      //click on run button
	  function runProjectPipe() {
	      saveRunIcon();
	      nxf_runmode = true;
	      var nextTextRaw = createNextflowFile("run");
	      nxf_runmode = false;
	      var nextText = encodeURIComponent(nextTextRaw);
	      var delIntermediate = '';
	      var profileTypeId = $('#chooseEnv').find(":selected").val(); //local-32
	      var patt = /(.*)-(.*)/;
	      var proType = profileTypeId.replace(patt, '$1');
	      var proId = profileTypeId.replace(patt, '$2');
	      //xxx not working
	      if (proType === 'cluster') {
              document.getElementById("runProPipe").style.display = "none";
              document.getElementById("connectingProPipe").style.display = "inline";
	      }
	      var configTextRaw = '';
	      if ($('#docker_check').is(":checked") === true) {
	          var docker_img = $('#docker_img').val();
	          configTextRaw += 'process.container = \'' + docker_img + '\'\n';
	          configTextRaw += 'docker.enabled = true\n';
	      }
	      if ($('#singu_check').is(":checked") === true) {
	          var singu_img = $('#singu_img').val();
	          configTextRaw += 'process.container = \'' + singu_img + '\'\n';
	          configTextRaw += 'singularity.enabled = true\n';
	      }
	      //all process settings eg. process.queue = 'short'
	      if ($('#exec_all').is(":checked") === true) {
	          var exec_all_settingsRaw = $('#allProcessSettTable').find('input');
	          var exec_all_settings = formToJson(exec_all_settingsRaw);
	          for (var keyParam in exec_all_settings) {
	              if (keyParam === 'time') {
	                  configTextRaw += 'process.' + keyParam + ' = \'' + exec_all_settings[keyParam] + 'm\'\n';
	              } else if (keyParam === 'cpu') {
	                  configTextRaw += 'process.' + keyParam + 's = \'' + exec_all_settings[keyParam] + '\'\n';
	              } else {
	                  configTextRaw += 'process.' + keyParam + ' = \'' + exec_all_settings[keyParam] + '\'\n';
	              }
	          };
	      }
	      //          if ($('#exec_each').is(":checked") === true) {
	      //	      var exec_each_settingsRaw = $('#processTable').find('input');
	      //	      var exec_each_settings = formToJson(exec_each_settingsRaw);
	      //	      }

	      //          process.$hello.queue = 'long'
	      console.log(configTextRaw);
	      var configText = encodeURIComponent(configTextRaw);
	      //save nextflow text as nextflow.nf and start job
	      var serverLogGet = getValues({
	          p: "saveRun",
	          nextText: nextText,
	          configText: configText,
	          profileType: proType,
	          profileId: proId,
	          project_pipeline_id: project_pipeline_id
	      });
	      console.log(serverLogGet);
	      if (proType === 'local') {
	          if (serverLogGet.next_submit_pid) {
	              $('#connectingProPipe').css('display', 'none');
	              $('#runLogs').css('display', 'inline');
	              $('#runProPipe').css('display', 'none');
	              $('#runningProPipe').css('display', 'inline');
	              checkServerLogTimer(proType, proId);
	          } else {
	              $('#runLogs').css('display', 'inline');
	              $('#runLogArea').val(serverLogGet);
	          }
	      } else if (proType === 'cluster') {
	          if (serverLogGet.next_submit_pid) {
	              $('#connectingProPipe').css('display', 'none');
	              $('#runLogs').css('display', 'inline');
	              $('#runProPipe').css('display', 'none');
	              $('#runningProPipe').css('display', 'inline');
	              checkServerLogTimer(proType, proId);
	          } else {
	              $('#runLogs').css('display', 'inline');
	              $('#runLogArea').val(serverLogGet);
	          }
	      }
	  }

	  function checkServerLogTimer(proType, proId) {
	      interval_serverlog_ID = setInterval(function () {
	          status = checkServerLog(project_pipeline_id, proType, proId)
	      }, 3000);
	  }

	  function checkServerLog(project_pipeline_id, proType, proId) {

	      if (proType === 'local') {
	          clearInterval(interval_serverlog_ID);
	          checkNextflowLogTimer(proType, proId);
	      }
	      if (proType === 'cluster') {
	          serverLog = getServerLog(project_pipeline_id);
	          $('#runLogArea').val(serverLog);
	          var reg = /Job <(.*)> is submitted to queue/;
	          var found = serverLog.match(reg);
	          //          console.log(found.length);
	          if (found && found.length > 0) {
	              var runPid = found[1];
	              //updateRunPid
	              var updateRunPidSend = getValues({ p: "updateRunPid", pid: runPid, project_pipeline_id: project_pipeline_id });
	              if (updateRunPidSend) {
	                  clearInterval(interval_serverlog_ID);
	                  checkNextflowLogTimer(proType, proId);
	              }
	          } else {
	              console.log("Error. Run not submitted");
	              clearInterval(interval_serverlog_ID);
	          }
	      }
	  }

	  function checkNextflowLogTimer(proType, proId) {
	      var runPid = getRunPid(project_pipeline_id);
	      var status = "";
	      interval_nextlog_ID = setInterval(function () {
	          status = checkRunPid(runPid, proType, proId)
	          console.log(status)
	      }, 3000);
	  }


	  function checkRunPid(runPid, proType, proId) {

	      var checkRunStatus = getValues({ p: "checkRunPid", pid: runPid, profileType: proType, profileId: proId });
	      console.log(checkRunStatus);

	      if (proType === 'local') {
	          if (checkRunStatus === "running") {
	              var runLog = getNextflowLog(project_pipeline_id, proType);
	              $('#runLogArea').val(runLog);
	              return checkRunStatus;
	          } else if (checkRunStatus === "completed") {
	              var runLog = getNextflowLog(project_pipeline_id, proType);
	              var updateRunPidComp = getValues({ p: "updateRunPid", pid: 0, project_pipeline_id: project_pipeline_id });
	              $('#runLogArea').val(runLog);
	              $('#runningProPipe').css('display', 'none');
	              $('#completeProPipe').css('display', 'inline');
	              clearInterval(interval_nextlog_ID);
	          }
	      } else if (proType === 'cluster') {
	          if (checkRunStatus === "running") {
	              nextflowLog = getNextflowLog(project_pipeline_id, proType, proId);
	              if (nextflowLog !== null) {
	                  $('#runLogArea').val(serverLog + nextflowLog);
	              }
	              return checkRunStatus;
	          } else if (checkRunStatus === "completed") {
	              nextflowLog = getNextflowLog(project_pipeline_id, proType, proId);
	              var updateRunPidComp = getValues({ p: "updateRunPid", pid: 0, project_pipeline_id: project_pipeline_id });
	              $('#runLogArea').val(serverLog + nextflowLog);
	              if (updateRunPidComp) {
	                  $('#runningProPipe').css('display', 'none');
	                  $('#completeProPipe').css('display', 'inline');
	                  clearInterval(interval_nextlog_ID);
	              }
	          }
	      }
	  }

	  function getNextflowLog(project_pipeline_id, proType, proId) {
	      if (proType === "local") {
	          var logText = getServerLog(project_pipeline_id);
	          return logText;
	      } else if (proType === "cluster") {
	          var logText = getValues({
	              p: "getNextflowLog",
	              project_pipeline_id: project_pipeline_id,
	              profileType: proType,
	              profileId: proId
	          });
	          return logText;
	      }
	  }


	  function getServerLog(project_pipeline_id) {

	      var logText = getValues({
	          p: "getServerLog",
	          project_pipeline_id: project_pipeline_id
	      });
	      return logText;
	  }

	  function getRunPid(project_pipeline_id) {

	      var runData = getValues({
	          p: "getRun",
	          project_pipeline_id: project_pipeline_id
	      });
	      var runPid = runData[0].pid;
	      return runPid;
	  }

	  function formToJson(rawFormData, stringify) {
	      var formDataSerial = rawFormData.serializeArray();
	      var formDataArr = {};
	      $.each(formDataSerial, function (el) {
	          formDataArr[formDataSerial[el].name] = formDataSerial[el].value;
	      });
	      if (stringify && stringify === 'stringify') {
	          return JSON.stringify(formDataArr);
	      } else {
	          return formDataArr;
	      }

	  }

	  function saveRunIcon() {
	      var data = [];
	      project_pipeline_id = $('#pipeline-title').attr('projectpipelineid');
	      var runSummary = $('#runSum').val();
	      var run_name = $('#run-title').val();
	      var output_dir = $('#rOut_dir').val();
	      var profile = $('#chooseEnv').val();
	      var perms = $('#perms').val();
	      var interdel = $('#intermeDel').is(":checked").toString();
	      var groupSel = $('#groupSel').val();
          var cmd = encodeURIComponent($('#runCmd').val());
	      var exec_each = $('#exec_each').is(":checked").toString();
	      var exec_all = $('#exec_all').is(":checked").toString();
	      var exec_next_settingsRaw = $('#execNextSettTable').find('input');
	      var exec_next_settings = formToJson(exec_next_settingsRaw, 'stringify');
	      var exec_all_settingsRaw = $('#allProcessSettTable').find('input');
	      var exec_all_settings = formToJson(exec_all_settingsRaw, 'stringify');
	      var exec_each_settingsRaw = $('#processTable').find('input');
	      var exec_each_settings = formToJson(exec_each_settingsRaw, 'stringify');
	      var docker_check = $('#docker_check').is(":checked").toString();
	      var docker_img = $('#docker_img').val();
	      var singu_check = $('#singu_check').is(":checked").toString();
	      var singu_img = $('#singu_img').val();


	      if (run_name !== '') {
	          data.push({ name: "id", value: project_pipeline_id });
	          data.push({ name: "name", value: run_name });
	          data.push({ name: "summary", value: runSummary });
	          data.push({ name: "output_dir", value: output_dir });
	          data.push({ name: "profile", value: profile });
	          data.push({ name: "perms", value: perms });
	          data.push({ name: "interdel", value: interdel });
	          data.push({ name: "cmd", value: cmd });
	          data.push({ name: "group_id", value: groupSel });
	          data.push({ name: "exec_each", value: exec_each });
	          data.push({ name: "exec_all", value: exec_all });
	          data.push({ name: "exec_next_settings", value: exec_next_settings });
	          data.push({ name: "exec_all_settings", value: exec_all_settings });
	          data.push({ name: "exec_each_settings", value: exec_each_settings });
	          data.push({ name: "docker_check", value: docker_check });
	          data.push({ name: "docker_img", value: docker_img });
	          data.push({ name: "singu_check", value: singu_check });
	          data.push({ name: "singu_img", value: singu_img });
	          data.push({ name: "p", value: "saveProjectPipeline" });
	          console.log(data);
	          $.ajax({
	              type: "POST",
	              url: "ajax/ajaxquery.php",
	              data: data,
	              async: true,
	              success: function (s) {
	                  checkReadytoRun()
	                  refreshCreatorData(project_pipeline_id);
	              },
	              error: function (errorThrown) {
	                  alert("Error: " + errorThrown);
	              }
	          });
	      } else {
	          //xxx
	          //Changes are not saved. Please enter the run name. 
	      }
	  }


	  $(document).ready(function () {
	      project_pipeline_id = $('#pipeline-title').attr('projectpipelineid');
	      pipeData = getValues({ p: "getProjectPipelines", id: project_pipeline_id });
	      pipeline_id = pipeData[0].pipeline_id;
	      project_id = pipeData[0].project_id;
	      $('#pipeline-title').attr('pipeline_id', pipeline_id);
	      if (project_pipeline_id !== '' && pipeline_id !== '') {
	          loadPipelineDetails(pipeline_id);
	          loadProjectPipeline(pipeData);
	      }
	      //not allow to check both docker and singularity
	      $('#docker_check').change(function () {
	          if ($('#docker_check').is(":checked") && $('#singu_check').is(":checked")) {
	              $('#singu_check').trigger("click");
	          }
	      });
	      $('#singu_check').change(function () {
	          if ($('#docker_check').is(":checked") && $('#singu_check').is(":checked")) {
	              $('#docker_check').trigger("click");
	          }
	      });

	      $('#inputFilemodal').on('show.bs.modal', function (e) {
	          var button = $(e.relatedTarget);
	          $(this).find('form').trigger('reset');
	          var clickedRow = button.closest('tr');
	          var rowID = clickedRow[0].id; //#inputTa-3
	          var gNumParam = rowID.split('-')[1];

	          if (button.attr('id') === 'inputFileSelect') {
	              $('#filemodaltitle').html('Select/Add Input File');
	              $('#mIdFile').attr('rowID', rowID);
	          } else if (button.attr('id') === 'inputFileEdit') {
	              $('#filemodaltitle').html('Change Input File');
	              $('#mIdFile').attr('rowID', rowID);
	              var proPipeInputID = $('#' + rowID).attr('propipeinputid');
	              $('#mIdFile').val(proPipeInputID);
	              // Get the input id of proPipeInput;
	              var proInputGet = getValues({ "p": "getProjectPipelineInputs", "id": proPipeInputID });
	              var input_id = proInputGet[0].input_id;
	              var inputGet = getValues({ "p": "getInputs", "id": input_id })[0];
	              //insert data into form
	              var formValues = $('#inputFilemodal').find('input');
	              var keys = Object.keys(inputGet);
	              for (var i = 0; i < keys.length; i++) {
	                  $(formValues[i]).val(inputGet[keys[i]]);
	              }

	          }
	      });

	      $('#inputFilemodal').on('click', '#selectfile', function (e) {
	          e.preventDefault();
	          var savetype = $('#mIdFile').val();
	          if (!savetype.length) { //add item
	              var checkTab = $('#inputFilemodal').find('.active');
	              var checkdata = checkTab[1].getAttribute('id');
	              if (checkdata === 'manualTab') {
	                  var formValues = $('#inputFilemodal').find('input');
	                  var data = formValues.serializeArray(); // convert form to array
	                  // check if name is entered 
	                  if (data[1].value !== '') {
	                      saveFileSetValModal(data, 'file');
	                      $('#inputFilemodal').modal('hide');
	                  }
	              }
	          } else { //edit item
	              var formValues = $('#inputFilemodal').find('input');
	              var data = formValues.serializeArray(); // convert form to array
	              // check if file_path is entered //xx?
	              if (data[1].value !== '') {
	                  editFileSetValModal(data, 'file');
	                  $('#inputFilemodal').modal('hide');
	              }
	          }
	      });

	      $('#inputsTab').on('click', '#inputDelDelete, #inputValDelete', function (e) {

	          var clickedRow = $(this).closest('tr');
	          var rowID = clickedRow[0].id; //#inputTa-3
	          var gNumParam = rowID.split('-')[1];
	          var proPipeInputID = $('#' + rowID).attr('propipeinputid');
	          var removeInput = getValues({ "p": "removeProjectPipelineInput", id: proPipeInputID });
	          var qualifier = $('#' + rowID + ' > :nth-child(4)').text();
	          removeSelectFile(rowID, qualifier);
	          checkReadytoRun();
	      });

	      $('#inputValmodal').on('show.bs.modal', function (e) {
	          var button = $(e.relatedTarget);
	          $(this).find('form').trigger('reset');
	          var clickedRow = button.closest('tr');
	          var rowID = clickedRow[0].id; //#inputTa-3
	          var gNumParam = rowID.split('-')[1];
	          if (button.attr('id') === 'inputValEnter') {
	              $('#valmodaltitle').html('Add Value');
	              $('#mIdVal').attr('rowID', rowID);
	          } else if (button.attr('id') === 'inputValEdit') {
	              $('#valmodaltitle').html('Edit Value');
	              $('#mIdVal').attr('rowID', rowID);
	              var proPipeInputID = $('#' + rowID).attr('propipeinputid');
	              $('#mIdVal').val(proPipeInputID);
	              // Get the input id of proPipeInput;
	              var proInputGet = getValues({ "p": "getProjectPipelineInputs", "id": proPipeInputID });
	              var input_id = proInputGet[0].input_id;
	              var inputGet = getValues({ "p": "getInputs", "id": input_id })[0];
	              //insert data into form
	              var formValues = $('#inputValmodal').find('input');
	              var keys = Object.keys(inputGet);
	              for (var i = 0; i < keys.length; i++) {
	                  $(formValues[i]).val(inputGet[keys[i]]);
	              }

	          }
	      });

	      $('#inputValmodal').on('click', '#saveValue', function (e) {
	          e.preventDefault();
	          var savetype = $('#mIdVal').val();
	          if (!savetype.length) { //add item
	              var formValues = $('#inputValmodal').find('input');
	              var data = formValues.serializeArray(); // convert form to array
	              // check if value is entered 
	              if (data[1].value !== '') {
	                  saveFileSetValModal(data, 'val');
	                  $('#inputValmodal').modal('hide');
	              }
	          } else { //edit item
	              var formValues = $('#inputValmodal').find('input');
	              var data = formValues.serializeArray(); // convert form to array
	              var filePath = data[1].value;
	              // check if file_path is entered //xx?
	              if (filePath !== '') {
	                  editFileSetValModal(data, 'val');
	                  $('#inputValmodal').modal('hide');
	              }
	          }
	      });

	      $(function () {
	          $(document).on('change', '#chooseEnv', function (event) {
	              var selPipeRev = $('#chooseEnv option:selected').val();
	              checkReadytoRun();
	          })
	      });

	      $('#confirmModal').on('show.bs.modal', function (e) {
	          var button = $(e.relatedTarget);
	          if (button.attr('id') === 'deleteRun' || button.attr('id') === 'delRun') {
	              $('#confirmModalText').html('Are you sure you want to delete this run?');
	          }
	      });

	      $('#confirmModal').on('click', '#deleteBtn', function (e) {
	          e.preventDefault();
	          project_pipeline_id = $('#pipeline-title').attr('projectpipelineid');
	          $.ajax({
	              type: "POST",
	              url: "ajax/ajaxquery.php",
	              data: {
	                  id: project_pipeline_id,
	                  p: "removeProjectPipeline"
	              },
	              async: true,
	              success: function (s) {
	                  window.location.replace("index.php?np=2&id=" + project_id);
	              },
	              error: function (errorThrown) {
	                  alert("Error: " + errorThrown);
	              }
	          });
	      });


	      //xxx
	  });