#Include "andev/http.bi"
#Include "andev/strings.bi"
#Include "file.bi"

Dim resp As http.http_response
Dim kbid As Integer
Dim url As String
Dim kbstr As String
Dim file As String
Dim cmd As String

For kbid = 0 To 9999
	kbstr = "Q-1" & PadLeft(Str(kbid), 5, "0")
	file = ExePath & "\kbase\" & kbstr & ".html"
	
	If FileExists(file) = 0 Then
		url = "http://www.tobit.de/login/kbArticle.asp?ArticleID=" & kbid
		resp = http.http_get(url, "", 1000)
		
		If resp.nStatus = 200 Then
			If InStr(resp.sData, "Q-1") > 0 Then
				Print file

				cmd = "wget --no-check-certificate -nv -O " & file & " """ & url & """"
				'Print cmd
				Shell cmd
			EndIf
		EndIf
	Else
		Print kbstr & " found"
	EndIf
Next

Print
Print "key..."
Sleep
