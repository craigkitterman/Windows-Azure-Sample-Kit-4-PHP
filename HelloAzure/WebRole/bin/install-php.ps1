[Reflection.Assembly]::LoadWithPartialName("Microsoft.WindowsAzure.ServiceRuntime")

if ([Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::CurrentRoleInstance.Id.Contains('_IN_')) {
    .\install-php-impl.cmd
}