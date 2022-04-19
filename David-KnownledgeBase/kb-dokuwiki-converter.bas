#Include "andev/strings.bi"
#Include "file.bi"

Function strip_tags(ByVal instring As String) As String
	Dim p As Integer
	Dim m As Integer = Len(instring) - 1
	Dim outstring As String
	Dim char As String
	Dim add_char As Byte = 1
	
	For p = 0 To m
		If instring[p] = 60 Then
			'Print instring[p] & " = < ######"
			add_char = 0
		ElseIf instring[p] = 62 Then
			'Print instring[p] & " = > ######"
			add_char = 1
		Else
			If add_char = 1 Then
				'Print instring[p] & " = " & Chr(instring[p])
				outstring = outstring & Chr(instring[p])
			Else
				'Print instring[p] & " = " & Chr(instring[p]) & " ######"
			EndIf
		EndIf
	Next
	
	Return outstring
End Function

Dim kbid As Integer
Dim kbstr As String
Dim infile As String
Dim outfile As String
Dim indata As String
Dim outdata As String
Dim tmp As String
Dim p1 As Integer
Dim p2 As Integer
Dim p3 As Integer
Dim f As Integer
Dim block_start As Integer
Dim block_end As Integer

block_start = 10989 : block_end = 10989

'block_start = 10000 : block_end = 10199
block_start = 10200 : block_end = 10499
'block_start = 10500 : block_end = 10799
'block_start = 10800 : block_end = 10989
'block_start = 10247 : block_end = 10247
'block_start = 8000 : block_end = 9999
block_start = 31 : block_end = 7999

For kbid = block_start To block_end
	kbstr = "Q-1" & PadLeft(Str(kbid), 5, "0")
	infile = ExePath & "\kbase\" & kbstr & ".html"
	outfile = ExePath & "\dokuwiki-kb\" & LCase(Left(kbstr, 5) & "." & Mid(kbstr, 6)) & ".txt"

	If FileExists(infile) = -1 Then
		If 1 = 1 Then
			f = FreeFile
			Open infile For Binary As #f
				indata = Space(Lof(f))
				Get #f, , indata
			Close #f
			
			p1 = InStr(indata, "<div class=""title"">")
			p2 = InStr(indata, "Antwort")
			p3 = InStr(p2, indata, "</table>")
			tmp = Mid(indata, p1, p3-p1)
			
			If p1 > 0 Then
				tmp = StrReplace(tmp, ">" & Chr(13, 10), ">")
				tmp = StrReplace(tmp, ">" & Chr(13, 10), ">")
				
				' title
				'tmp = StrReplace(tmp, "<div class=""title"">", "====== ")
				'tmp = StrReplace(tmp, "</div>          ", " ======")
				
				' subtitle
				'tmp = StrReplace(tmp, "<b class=tabletext style=""position:relative; top:-5px;"">", "===== ")
				'tmp = StrReplace(tmp, "</b><p style=""position:relative; top:-14px;""><br>", " =====" & Chr(13, 10))

				' title + subtitle
				tmp = StrReplace(tmp, "<div class=""title"">", "====== ")
				tmp = StrReplace(tmp, "</div>         ", " - ")
				tmp = StrReplace(tmp, Chr(13, 10) & "<b class=tabletext style=""position:relative; top:-5px;"">", "")
				tmp = StrReplace(tmp, "</b><p style=""position:relative; top:-14px;""><br>", " ======" & Chr(13, 10) & "~~NOCACHE~~" & Chr(13, 10) & "!!!")

				' cleanup (frage)
				tmp = StrReplace(tmp, "<table cellspacing=""0"" cellpadding=""2"" class=""border"" ID=""Table1"">", "")
				tmp = StrReplace(tmp, "  <tr>    <td colspan=""2"" class=""borderhead"" style=""padding-left:5px;""><b>Frage</b></td>  </tr>  ", "")
				tmp = StrReplace(tmp, "<tr>    <td colspan=""2"" class=""tabletext"" height=""5""></td>  </tr>  ", "")
				
				' Problem
				tmp = StrReplace(tmp, "    <tr>      <td width=""70"" valign=""top"" align=""right"" class=""tabletext"" style=""padding-right:10px;""><b>", Chr(13, 10) & "==== ")
				tmp = StrReplace(tmp, "</b></td>      <td class=""tabletext"" style=""padding-right:10px;"">", " ====" & Chr(13, 10) & Chr(13, 10))
				
				' Produkt
				tmp = StrReplace(tmp, "    <tr>      <td width=""70"" align=""right"" class=""tabletext"" style=""padding-right:10px;""><b>", Chr(13, 10) & Chr(13, 10) & "==== ")
				tmp = StrReplace(tmp, "</b></td>      <td class=""tabletext"">", " ====" & Chr(13, 10) & Chr(13, 10))
				
				' Datum
				tmp = StrReplace(tmp, "  <tr>    <td width=""70"" align=""right"" class=""tabletext"" style=""padding-right:10px;""><b>", Chr(13, 10) & Chr(13, 10) & "==== ")
				tmp = StrReplace(tmp, "</b></td>    <td class=""tabletext"">", " ====" & Chr(13, 10) & Chr(13, 10))
				
				' Antwort
				tmp = StrReplace(tmp, "</td>  </tr>  <tr>    <td colspan=""2"" class=""tabletext"" height=""5""></td>  </tr></table></p><p><table cellpadding=""2"" cellspacing=""0"" class=""border"">  <tr>    <td class=""borderhead"" style=""padding-left:5px""><b>", Chr(13, 10) & Chr(13, 10) & Chr(13, 10) & "==== ")
				tmp = StrReplace(tmp, "</b></td>  </tr>  <tr>    <td class=""tabletext"" height=""5""></td>  </tr>  <tr>    <td class=""tabletext"" style=""padding-left:10px; padding-right:10px;"">", " ====" & Chr(13, 10) & Chr(13, 10))
				
				tmp = StrReplace(tmp, "<p>", Chr(13, 10) & Chr(13, 10))
				
				' cleanup 
				tmp = StrReplace(tmp, "</td>    </tr>    ", "")
				tmp = StrReplace(tmp, "</p></td>  </tr>  <tr>    <td class=""tabletext"" height=""5""></td>  </tr>", "")
				tmp = StrReplace(tmp, Chr(13, 10) & Chr(13, 10) & "====", Chr(13, 10) & "====")
				
				tmp = StrReplace(tmp, "&amul;", "ä")
				tmp = StrReplace(tmp, "&omul;", "ö")
				tmp = StrReplace(tmp, "&umul;", "ü")
				
				'tmp = StrReplace(tmp, "xxx", "yyy")
				
				tmp = tmp & Chr(13, 10) & "==== Original-Link ====" & Chr(13, 10)
				tmp = tmp & "[[http://www.tobit.de/login/kbArticle.asp?ArticleID=" & kbid & "]]"

				tmp = strip_tags(tmp)

				tmp = StrReplace(tmp, "  ", " ")

				Print tmp
				
				'f = FreeFile
				'Open ExePath & "\x-debug.txt" For Output As #f
				'	Print #f, tmp
				'Close #f

				f = FreeFile
				Open outfile For Output As #f
					Print #f, tmp
				Close #f
			EndIf
		EndIf
	EndIf
Next

Print
Print "key..."
'Sleep 1000
