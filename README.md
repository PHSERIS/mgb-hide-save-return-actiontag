# OVERVIEW - MGB HIDESAVERETURN Action Tag

## Summary
This module adds the @HIDESAVERETURN as an action tag that can be added to any field. If the field is visible on a survey (ie,, any branching  logic for the field evaluates to true),  it inserts javascript that hides the "Save & return" button.

## Why?
There  are specific use cases  where the "Save & Return" button need need to be hidden from particular surveys within a project.

 ## How?
Add @HIDESAVERETURN  to the "Action Tags / Field Annotation" field.

![Screenshot 1](images/image1.png)


This differs from the HideSaveReturnBtn external module in that HideSaveReturnBtn disables the button for all pages in all  surveys of a project. Instead, this just adds an action tag  that can be conditionally applied using branching logic.