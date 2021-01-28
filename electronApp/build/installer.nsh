!macro customHeader

!macroend

!macro preInit

!macroend

!macro customInit
        # guid=7e51495b-3f4d-5235-aadd-5636863064f0
        ReadRegStr $0 HKLM "SOFTWARE\Microsoft\Windows\CurrentVersion\Uninstall\{7e51495b-3f4d-5235-aadd-5636863064f0}" "UninstallString"
        ${If} $0 != ""
            MessageBox MB_ICONINFORMATION|MB_TOPMOST  "检测到系统中已安装本程序，将卸载旧版本" IDOK
            # ExecWait $0 $1
        ${EndIf}
!macroend

!macro customInstall

!macroend

!macro customInstallMode
  # set $isForceMachineInstall or $isForceCurrentInstall
  # to enforce one or the other modes.
  #set $isForceMachineInstall
!macroend
